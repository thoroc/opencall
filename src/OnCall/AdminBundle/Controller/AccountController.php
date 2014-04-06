<?php

namespace OnCall\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use OnCall\AdminBundle\Menu\MenuHandler;
use OnCall\AdminBundle\Entity\User;
use OnCall\AdminBundle\Entity\Client;
use OnCall\AdminBundle\Model\Controller;
use OnCall\PlivoBundle\AccountCounter\Repository as ACRepo;
use OnCall\PlivoBundle\AccountCounter\Entry as ACEntry;
use DateTime;

class AccountController extends Controller
{
    public function indexAction()
    {
        // get accounts (all users who have no roles (ROLE_USER)
        $dql = 'select u from OnCall\AdminBundle\Entity\User u where u.roles = :role';
        $query = $this->getDoctrine()
            ->getManager()
            ->createQuery($dql)
            ->setParameter('role', 'a:0:{}');
        $accounts = $query->getResult();
        // replace the above by a repo method
//         $accounts = $this->getDoctrine->getManager()
//                          ->getRepository( 'onCallAdminBundle:User' )
//                          ->getUserWithRoles();

        // get role hash for menu
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();
        $alerts = array();

        $aggr_count = array();
        $conn = $this->get('database_connection');
        $ac_repo = new ACRepo($conn->getWrappedConnection());
        $res = $ac_repo->fetchAll();
        foreach ($res as $ac)
        {
            $user_id = $ac->getUserID();
            if (!isset($user_id))
                $aggr_count[$user_id] = array();

            $aggr_count[$user_id][] = $ac;
        }

        $params = $this->getRequest()->query->all();

        return $this->render(
            'OnCallAdminBundle:Account:index.html.twig',
            array(
                'sidebar_menu' => MenuHandler::getMenu($role_hash, 'account', null, $params),
                'alerts' => $alerts,
                'accounts' => $accounts,
                'aggr_count' => $aggr_count
            )
        );

    }

    public function createAction()
    {
        // add user
        try
        {
            $mgr = $this->get('fos_user.user_manager');
            $user = $mgr->createUser();

            $data = $this->getRequest()->request->all();
            $this->updateUser($user, $data);
            $mgr->updateUser($user);
        }
        catch (DBALException $e)
        {
            $this->addFlash('error', 'Could not create account, username probably exists.');
        }

        // add client
        try
        {
            $em = $this->getDoctrine()->getManager();

            // add default client for non-multi-client
            if (!$user->isMultiClient())
            {
                $client = new Client();
                $client->setUser($user)
                    ->setName($user->getBusinessName())
                    ->setTimezone('8.0');
                $em->persist($client);
                $em->flush();
            }
        }
        catch (DBALException $e)
        {
            $this->addFlash('error', 'Could not add default client for account.');
            error_log($e->getMessage());
        }

        // initialize counters
        $conn = $this->get('database_connection');
        $ac_repo = new ACRepo($conn->getWrappedConnection());
        $ac_repo->initialize(new DateTime(), $user->getID());

        return $this->redirect($this->generateUrl('oncall_admin_accounts'));
    }

    protected function updateUser(User $user, $data)
    {
        $user->setName($data['name'])
            ->setEmail($data['email'])
            ->setBusinessName($data['business_name'])
            ->setPhone($data['phone'])
            ->setAddress($data['address'])
            ->setBillBusinessName($data['bill_business_name'])
            ->setBillName($data['bill_name'])
            ->setBillEmail($data['bill_email'])
            ->setBillPhone($data['bill_phone'])
            ->setBillAddress($data['bill_address'])
            ->setEnabled($data['enabled'])
            ->setRoles(array('ROLE_USER'));

        // username is not set on edit
        if (isset($data['username']) && !empty($data['username']))
            $user->setUsername($data['username']);

        // check if password was specified
        if (!empty($data['password']))
            $user->setPlainPassword($data['password']);

        // check if multi-client
        if (isset($data['multi_client']) && $data['multi_client'] == 1)
            $user->setMultiClient(true);
        else
            $user->setMultiClient(false);
    }

    public function getAction($id)
    {
        $mgr = $this->get('fos_user.user_manager');
        $edit_user = $mgr->findUserBy(array('id' => $id));

        return new Response($edit_user->jsonify());
    }

    public function updateAction($id)
    {
        // find user
        $mgr = $this->get('fos_user.user_manager');
        $edit_user = $mgr->findUserBy(array('id' => $id));

        $data = $this->getRequest()->request->all();

        // update user data and persist
        $this->updateUser($edit_user, $data);
        $mgr->updateUser($edit_user);

        $this->addFlash('success', 'Account ' . $edit_user->getUsername() . ' updated.');

        return $this->redirect($this->generateUrl('oncall_admin_accounts'));
    }

    public function passwordFormAction()
    {
        $user = $this->getUser();
        $role_hash = $user->getRoleHash();
        $params = $this->getRequest()->query->all();

        return $this->render(
            'OnCallAdminBundle:Account:password.html.twig',
             array(
                'sidebar_menu' => MenuHandler::getMenu($role_hash, null, null, $params),
                'user' => $user
            )
        );
    }

    public function passwordSubmitAction()
    {
        $data = $this->getRequest()->request->all();
        $trans = $this->get('translator');

        // field check
        if (!isset($data['pass1']) || !isset($data['pass2']) || empty($data['pass1']) || empty($data['pass2']))
        {
            $this->addFlash('error', $trans->trans('acc.msg.blank'));
            return $this->redirect($this->generateUrl('oncall_admin_password_form'));
        }

        // match check
        if ($data['pass1'] != $data['pass2'])
        {
            $this->addFlash('error', $trans->trans('acc.msg.match'));
            return $this->redirect($this->generateUrl('oncall_admin_password_form'));
        }

        // change password
        $user = $this->getUser();
        $user->setPlainPassword($data['pass1']);
        $mgr = $this->get('fos_user.user_manager');
        $mgr->updateUser($user);

        $this->addFlash('success', $trans->trans('acc.msg.success'));

        return $this->redirect($this->generateUrl('oncall_admin_password_form'));
    }
}
