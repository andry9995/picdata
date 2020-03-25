<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/01/2018
 * Time: 09:14
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationImageCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('app:notification:image')
            ->setDescription("Email rappel images manquantes")
            ->setHelp("Cette commande permet de generer des emails de rappel des images non parvenues.")
            ->addOption(
                'generate-only',
                null,
                InputOption::VALUE_NONE,
                'Generer les emails mais pas envoyer'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Creation des emails a envoyer.",
            "=============================================",
            "",
        ]);

        $notification_handler = $this->getContainer()
            ->get('echange.notification_image');
        $notification_handler->bootstrap();

        $output->writeln([
            "Fini.",
            "=============================================",
            "",
        ]);

        if ($input->getOption("generate-only") === false) {
            $sendmail = $this->getApplication()->find('app:sendmail');
            $sendmail->run($input, $output);
        }
    }
}