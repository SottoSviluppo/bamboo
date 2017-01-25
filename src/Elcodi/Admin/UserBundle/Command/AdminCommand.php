<?php

namespace Elcodi\Admin\UserBundle\Command;

use Elcodi\Component\Core\Command\Abstracts\AbstractElcodiCommand;
use Elcodi\Component\User\ElcodiUserProperties;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdminCommand extends AbstractElcodiCommand
{
    protected $container;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('elcodi:admin:create')->setDescription('Create admin user');
        $this->addArgument('email', InputArgument::REQUIRED, 'Admin email address');
        $this->addArgument('first_name', InputArgument::REQUIRED, 'Admin first name');
        $this->addArgument('last_name', InputArgument::REQUIRED, 'Admin last name');
        $this->addArgument('psw', InputArgument::REQUIRED, 'Admin Password');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $defaultEmail = "admin@admin.it";
        $defaultFirstName = "Mario";
        $defaultLastName = "Bianchi";
        $defaultPassword = "1234";

        $email = $this->getHelper('dialog')->askAndValidate($output, "<question>Admin email:</question> [<comment>$defaultEmail</comment>]", function ($typeInput) {
            return $typeInput;
        }, 10, $defaultEmail);
        $firstName = $this->getHelper('dialog')->askAndValidate($output, "<question>First name:</question> [<comment>$defaultFirstName</comment>] ", function ($typeInput) {
            return $typeInput;
        }, 10, $defaultFirstName);
        $lastName = $this->getHelper('dialog')->askAndValidate($output, "<question>Last name:</question> [<comment>$defaultLastName</comment>] ", function ($typeInput) {
            return $typeInput;
        }, 10, $defaultLastName);
        $password = $this->getHelper('dialog')->askAndValidate($output, "<question>Password:</question> [<comment>$defaultPassword</comment>] ", function ($typeInput) {
            return $typeInput;
        }, 10, $defaultPassword);

        $input->setArgument('email', $email);
        $input->setArgument('first_name', $firstName);
        $input->setArgument('last_name', $lastName);
        $input->setArgument('psw', $password);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getApplication()->getKernel()->getContainer();
        $this->startCommand($output)->createData($input, $output)->finishCommand($output);
    }

    protected function createData(InputInterface $input, OutputInterface $output)
    {
        $this->printMessage($output, 'Crate Admin User', 'Starting');
        $email = $input->getArgument('email');
        $firstName = $input->getArgument('first_name');
        $lastName = $input->getArgument('last_name');
        $password = $input->getArgument('psw');

        try {
            $adminUserRepository = $this->container->get('elcodi.repository.admin_user');
            $adminUserFactory = $this->container->get('elcodi.factory.admin_user');
            $adminUserManager = $this->container->get('elcodi.object_manager.admin_user');

            $adminUser = $adminUserFactory
                ->create()
                ->setPassword($password)
                ->setEmail($email)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setGender(ElcodiUserProperties::GENDER_MALE)
                ->setEnabled(true);

            $adminUserManager->persist($adminUser);
            $adminUserManager->flush();

        } catch (\Exception $ex) {
            $this->printMessage(
                $output,
                'Error',
                $ex->getMessage() . " in " . $ex->getFile() . " on line " . $ex->getLine() . " - " . $ex->getTraceAsString()
            );
        }

        return $this;
    }
}
