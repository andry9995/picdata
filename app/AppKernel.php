<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new UtilisateurBundle\UtilisateurBundle(),
            new ModelBundle\ModelBundle(),
            new AdminBundle\AdminBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new MenuBundle\MenuBundle(),
            new DashboardBundle\DashboardBundle(),
            new ImageBundle\ImageBundle(),
            new EtatFinancierBundle\EtatFinancierBundle(),
            new AdminUserBundle\AdminUserBundle(),
            new DossierBundle\DossierBundle(),
            new ComptabiliteBundle\ComptabiliteBundle(),
            new EtatBaseBundle\EtatBaseBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Ensepar\Html2pdfBundle\EnseparHtml2pdfBundle(),
            new IndicateurBundle\IndicateurBundle(),
            new RubriqueBundle\RubriqueBundle(),
            new TypeGrapheBundle\TypeGrapheBundle(),
            new CodeAnalytiqueBundle\CodeAnalytiqueBundle(),
            new ChartBundle\ChartBundle(),
            new InfoPerdosBundle\InfoPerdosBundle(),
            new FacturationBundle\FacturationBundle(),
            new ConsultationPieceBundle\ConsultationPieceBundle(),
            new EtatBundle\EtatBundle(),
            new TableauImageBundle\TableauImageBundle(),
            new LightSuner\CarbonBundle\CarbonBundle(),
            new PcgBundle\PcgBundle(),
            new BanqueBundle\BanqueBundle(),
            new AideBundle\AideBundle(),
            new One\ProspectBundle\OneProspectBundle(),
            new One\VenteBundle\OneVenteBundle(),
            new One\UtilisateurBundle\OneUtilisateurBundle(),
            new CleBundle\CleBundle(),
            new NoteFraisBundle\NoteFraisBundle(),
            new One\AchatBundle\OneAchatBundle(),
            new LinxoBundle\LinxoBundle(),
            new DrtBundle\DrtBundle(),
            new AjaxLoginBundle\AjaxLoginBundle(),
            //new Nelmio\CorsBundle\NelmioCorsBundle(),
            new GeneralBundle\GeneralBundle(),
            new ZendeskBundle\ZendeskBundle(),
            new JournalBundle\JournalBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
