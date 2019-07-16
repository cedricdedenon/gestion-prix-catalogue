<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\DiscountedPriceCalculator;
use App\Service\EmailService;

class SendEmailCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:send-email';

    private $discountedPriceCalculator;
    private $emailService;
   
    public function __construct(DiscountedPriceCalculator $discountedPriceCalculator, EmailService $emailService)
    {
        parent::__construct();
        $this->discountedPriceCalculator = $discountedPriceCalculator;
        $this->emailService = $emailService;
    }


    protected function configure()
    {
        $this
        // Une courte description lorsque que l'on éxécute la commande "php bin/console list"
        ->setDescription('Envoyer un email avec les réductions')

        // Description entière de la commande en rajoutant l'option --help
        ->setHelp('Cette commande permet de remettre à jour les prix réduits de la totalité du catalogue de produit. Permet d\'envoyer un email récapitulatif')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);
        $this->discountedPriceCalculator->calculateDiscountedPriceForProduct();
        $this->emailService->sendEmail();
        $io->success('Les prix réduits ont été recalculés et un email a été envoyé');
    }

}