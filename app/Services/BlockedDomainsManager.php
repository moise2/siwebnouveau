<?php
namespace App\Services;

use Exception;

class BlockedDomainsManager
{
    /**
     * Liste des domaines bloqués.
     * @var array
     */
    private $blockedDomains = [
        // Exemples de domaines de spam connus
        'spamdomain.com', 'freemoney4u.net', 'get-rich-quick.biz', 'click-here-now.org',
        'win-big-today.info', 'phishingsite.net', 'malware-site.org', 'fakeupdates.com',
        'bademailservice.co', 'lottery-scam.com',

        // Exemples de domaines malveillants
        'stealyourinfo.ru', 'fakesupport123.com', 'virus-download.org', 'ransomware-hub.net',
        'data-thieves.biz', 'keyloggerz.com', 'ddos-tools.info', 'botnet-controllers.co',
        'hackeryourphone.net', 'malicious-email.co',

        // Exemples de faux sites bancaires
        'fakebanklogin.com', 'secure-your-account.net', 'bank-verification.biz',
        'fake-payments.co', 'phishing-banking.org', 'verifyyourbank.ru', 'bankfraudemail.com',
        'money-stealers.net', 'unsecure-bank.biz', 'paypal-fraudulent.com',

        // Exemples de domaines de piratage
        'freehacksdownloads.com', 'cheatsforallgames.net', 'unlockyourphone.co',
        'hackertools.biz', 'pirated-software.com', 'downloadcracks.org', 'illegaldownloads.net',
        'gametoolshacks.info', 'moddingtools.biz', 'hackz4u.net',

        // Exemples de sites frauduleux
        'freesoftwaregiveaway.net', 'freelicensekey.org', 'crackedversion.co',
        'fake-coupon-deals.com', 'illegalsales.biz', 'unverifieddeals.com', 
        'fakeoffersfree.net', 'scamdeals.biz', 'cheapfraudservices.co', 'counterfeititems.com',

        // Exemples de sites pornographiques malveillants
        'adult-malware-site.com', 'free-video-downloads.net', 'illegal-content.biz',
        'adultscams.co', 'unsecureadultsite.net', 'fraudulentporn.biz', 'dangerouscontent.org',
        'hiddenadultlinks.com', 'unsafevideos.info', 'malware-porn-links.biz',

        // Exemples de sites de fausses nouvelles
        'fake-news-site.org', 'untrusted-source.net', 'email-harvester.com',
        'stealpasswords.biz', 'dangerous-site.info', 'scamnews.ru', 'fakenewshub.net',
        'misinformation.biz', 'propaganda-portal.com', 'unverifiednews.info',

        // Exemples de domaines de contrefaçon
        'fake-brands.biz', 'counterfeitgoods.co', 'illegalmerchandise.net',
        'unlawfulsellers.com', 'piratedcontent.org', 'copycatproducts.biz', 
        'knockoffitems.net', 'fraudulentmerch.info', 'fakeelectronics.org',
        'scamsales.biz',

        // Exemples de domaines pour bots
        'botnetprovider.com', 'botscripts.net', 'botcontroller.biz',
        'maliciousbot.co', 'ddosbotserver.org', 'automationfraud.biz', 'unsecurebotnet.net',
        'botattacks.info', 'botoperators.biz', 'malwarebots.org',

        // Autres exemples
        'fake-drugstore.biz', 'counterfeitmeds.co', 'illegal-pharmacy.net',
        'fraudulenthealth.biz', 'fakecovidtest.com', 'illegalsupplements.org',
        'fraudulent-charity.net', 'fakesupportteam.biz', 'untrustedtechnicalsupport.com',
        'scamcallcenter.co'
    ];

    /**
     * Charge des domaines bloqués supplémentaires depuis un fichier externe ou une API.
     * @param array|string $source Chemin vers le fichier ou URL pour récupérer les domaines.
     * @return void
     */
    public function loadBlockedDomains($source)
    {
        if (is_string($source) && file_exists($source)) {
            $domains = file($source, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $this->blockedDomains = array_merge($this->blockedDomains, $domains);
        } elseif (is_array($source)) {
            $this->blockedDomains = array_merge($this->blockedDomains, $source);
        } else {
            throw new Exception("Source de domaines invalide.");
        }
    }

    /**
     * Vérifie si un domaine est bloqué.
     * @param string $domain Nom de domaine à vérifier.
     * @return bool
     */
    public function isDomainBlocked(string $domain): bool
    {
        return in_array($domain, $this->blockedDomains, true);
    }

    /**
     * Retourne la liste complète des domaines bloqués.
     * @return array
     */
    public function getBlockedDomains(): array
    {
        return $this->blockedDomains;
    }
}
