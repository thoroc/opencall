<?php

namespace OnCall\PlivoBundle\Factory;

use OnCall\PlivoBundle\Log\Entry as LogEntry;
use OnCall\PlivoBundle\Log\Repository as LogRepository;
use OnCall\PlivoBundle\Aggregate\Entry as AggEntry;
use OnCall\PlivoBundle\Aggregate\Repository as AggRepository;
use OnCall\PlivoBundle\Log\Pusher as LogPusher;
use PDO;
use DateTime;
use OnCall\PlivoBundle\AccountCounter\Repository as ACRepo;
use OnCall\PlivoBundle\AccountCounter\Entry as ACEntry;
use OnCall\PlivoBundle\Alert\Sender as AlertSender;
use OnCall\PlivoBundle\Alert\Repository as AlertRepo;
use OnCall\AdminBundle\Model\Timezone;

class Hangup extends Lockable
{
    protected $pdo;
    protected $zmq;

    public function __construct( PDO $pdo, $redis, $zmq )
    {
        parent::__construct( $redis );
        $this->pdo = $pdo;
        $this->zmq = $zmq;
    }

    protected function updateLog( LogEntry $log, $params )
    {
        $log->setBillRate( $params->getBillRate() )
                ->setBillDuration( $params->getBillDuration() )
                ->setDuration( $params->getDuration() )
                ->setDateStart( new DateTime( $params->getStartTime() ) )
                ->setDateEnd( new DateTime( $params->getEndTime() ) )
                ->setStatus( $params->getStatus() )
                ->setHangupCause( $params->getHangupCause() );

        return $log;
    }

    public function run( $post )
    {
        try
        {
            // parse parameters
            $params = new Parameters( $post );

            // lock call_id
            $this->lock( $params->getUniqueID() );

            // start log and aggregate
            // get log
            $log_repo = new LogRepository( $this->pdo );
            $log = $log_repo->find( $params->getUniqueID() );
            // no log entry found (no answer call?)
            if( $log == null )
            {
                error_log( 'no answer entry' );
                $this->unlock( $params->getUniqueID() );
                exit;
            }

            // update log with hangup data
            $this->updateLog( $log, $params );
            $log_repo->updateHangup( $log );

            // alerts
            $al_repo = new AlertRepo( $this->pdo );
            $al_sender = new AlertSender( $al_repo );
            $al_sender->send( $log );

            // aggregate
            $agg_repo = new AggRepository( $this->pdo );
            $agg = AggEntry::createFromLog( $log );
            $agg_repo->persist( $agg );

            // get client timezone
            $tzone = $log_repo->getClientTimezone( $log->getClientID() );
            $cl_tzone = $cl_tzone = Timezone::toPHPTimezone( $tzone );
            $log->getDateStart()->setTimezone( $cl_tzone );

            // live log
            $log_pusher = new LogPusher( $this->zmq );
            $log_pusher->send( $log );

            // TODO: account counter
            /*
              $num_data = $qmsg->getNumberData();
              $ac_repo = new ACRepo($this->pdo);
              $ac_entry = new ACEntry(new DateTime(), $num_data['user_id']);
              $ac_entry->setCall(1);
              $ac_entry->setDuration($qmsg->getHangupParams()->getDuration());
              $ac_repo->append($ac_entry);
             */

            // unlock call_id
            $this->unlock( $params->getUniqueID() );

            // end log and aggregate
        }
        catch( PDOException $e )
        {
            // catch pdo / db error
            error_log( 'pdo exception' );
        }
    }
}