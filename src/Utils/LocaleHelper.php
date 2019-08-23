<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use peterkahl\flagMaster\flagMaster;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;

class LocaleHelper
{
    /** @var Collection */
    private $localeCodes;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Collection */
    private $flagCodes;

    /** @var Config */
    private $config;

    public function __construct(string $locales, UrlGeneratorInterface $urlGenerator, Config $config)
    {
        $this->localeCodes = new Collection(explode('|', $locales));
        $this->urlGenerator = $urlGenerator;

        $this->flagCodes = $this->getFlagCodes();
        $this->config = $config;
    }

    public function getLocales(Environment $twig, bool $all = false): Collection
    {
        if ($all) {
            $localeCodes = $this->localeCodes;
        } else {
            $localeCodes = $this->getContentLocales();
        }
        // Get the route and route params, to set the new localized link
        $globals = $twig->getGlobals();

        /** @var Request $request */
        $request = $globals['app']->getRequest();
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');
        $currentLocale = $request->getLocale();

        $locales = new Collection();
        foreach ($localeCodes as $localeCode) {
            $locale = $this->localeInfo($localeCode);

            $locale->put('link', $this->getLink($route, $routeParams, $locale));
            $locale->put('current', $currentLocale === $localeCode);

            $locales->push($locale);
        }

        return $locales;
    }

    private function getContentLocales(): array
    {
        $contentTypes = $this->config->get('contenttypes');

        $locales = [];
        foreach ($contentTypes as $contentType) {
            $locales = array_merge($locales, $contentType->get('locales')->all());
        }

        return $locales;
    }

    private function getLink(string $route, array $routeParams, Collection $locale): string
    {
        switch ($route) {
            case 'record':
            case 'homepage':
            case 'listing':
            case 'search':
            case 'taxonomy':
                $route = $route .= '_locale';
                // no break
            case 'record_locale':
            case 'homepage_locale':
            case 'listing_locale':
            case 'search_locale':
            case 'taxonomy_locale':
                $routeParams['_locale'] = $locale->get('code');
                break;
            default:
                $routeParams['edit_locale'] = $locale->get('code');
        }

        return $this->urlGenerator->generate($route, $routeParams);
    }

    /**
     * @param string|Collection $localeCode
     */
    public function localeInfo($localeCode): Collection
    {
        if ($localeCode instanceof Collection) {
            $localeCode = $localeCode->get('code');
        }

        $splitCode = preg_split('/[_-]/', $localeCode);

        if (isset($splitCode[1])) {
            $localeCode = sprintf('%s_%s', mb_strtolower($splitCode[0]), mb_strtoupper($splitCode[1]));
        } else {
            $localeCode = mb_strtolower($splitCode[0]);
        }

        $flag = $this->getFlagCode($localeCode);

        return new Collection([
            'code' => $localeCode,
            'name' => Intl::getLocaleBundle()->getLocaleName($localeCode),
            'localizedname' => Intl::getLocaleBundle()->getLocaleName($localeCode, $localeCode),
            'flag' => $flag,
            'emoji' => flagMaster::emojiFlag($flag),
        ]);
    }

    private function getFlagCode($localeCode): string
    {
        $splitCode = preg_split('/[_-]/', $localeCode);

        // for codes like `en_GB`
        if (isset($splitCode[1]) && $this->flagCodes->get(mb_strtoupper($splitCode[1]))) {
            return $this->flagCodeFormatter($splitCode[1]);
        }

        // for codes like `nl`
        if ($this->flagCodes->get(mb_strtoupper($splitCode[0]))) {
            return $this->flagCodeFormatter($splitCode[0]);
        }

        return $this->flagCodeFormatter('blank');
    }

    private function flagCodeFormatter($flag): string
    {
        if ($flag === 'en') {
            return 'gb';
        }

        return mb_strtolower($flag);
    }

    private function getFlagCodes(): Collection
    {
        return new Collection([
            'AD' => 'Andorra',
            'AE' => 'United Arab Emirates',
            'AF' => 'Afghanistan',
            'AG' => 'Antigua & Barbuda',
            'AI' => 'Anguilla',
            'AL' => 'Albania',
            'AM' => 'Armenia',
            'AO' => 'Angola',
            'AR' => 'Argentina',
            'AS' => 'American Samoa',
            'AT' => 'Austria',
            'AU' => 'Australia',
            'AW' => 'Aruba',
            'AX' => 'Åland Islands',
            'AZ' => 'Azerbaijan',
            'BA' => 'Bosnia & Herzegovina',
            'BB' => 'Barbados',
            'BD' => 'Bangladesh',
            'BE' => 'Belgium',
            'BF' => 'Burkina Faso',
            'BG' => 'Bulgaria',
            'BH' => 'Bahrain',
            'BI' => 'Burundi',
            'BJ' => 'Benin',
            'BL' => 'St. Barthélemy',
            'BM' => 'Bermuda',
            'BN' => 'Brunei',
            'BO' => 'Bolivia',
            'BR' => 'Brazil',
            'BS' => 'Bahamas',
            'BT' => 'Bhutan',
            'BV' => 'Bouvet Island',
            'BW' => 'Botswana',
            'BY' => 'Belarus',
            'BZ' => 'Belize',
            'CA' => 'Canada',
            'CC' => 'Cocos (Keeling) Islands',
            'CD' => 'Congo - Kinshasa',
            'CF' => 'Central African Republic',
            'CG' => 'Congo - Brazzaville',
            'CH' => 'Switzerland',
            'CI' => 'Côte d’Ivoire',
            'CK' => 'Cook Islands',
            'CL' => 'Chile',
            'CM' => 'Cameroon',
            'CN' => 'China',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'CV' => 'Cape Verde',
            'CW' => 'Curaçao',
            'CX' => 'Christmas Island',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DE' => 'Germany',
            'DJ' => 'Djibouti',
            'DK' => 'Denmark',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'DZ' => 'Algeria',
            'EC' => 'Ecuador',
            'EE' => 'Estonia',
            'EG' => 'Egypt',
            'ER' => 'Eritrea',
            'ES' => 'Spain',
            'ET' => 'Ethiopia',
            'EU' => 'European Union',
            'FI' => 'Finland',
            'FJ' => 'Fiji',
            'FK' => 'Falkland Islands',
            'FM' => 'Micronesia',
            'FO' => 'Faroe Islands',
            'FR' => 'France',
            'GA' => 'Gabon',
            'EN' => 'United Kingdom', // Alias
            'GB' => 'United Kingdom',
            'GB-ENG' => 'United Kingdom',
            'GB-NIR' => 'United Kingdom',
            'GB-SCT' => 'United Kingdom',
            'GB-WLS' => 'United Kingdom',
            'GB-ZET' => 'United Kingdom',
            'GD' => 'Grenada',
            'GE' => 'Georgia',
            'GF' => 'French Guiana',
            'GG' => 'Guernsey',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GL' => 'Greenland',
            'GM' => 'Gambia',
            'GN' => 'Guinea',
            'GP' => 'Guadeloupe',
            'GQ' => 'Equatorial Guinea',
            'GR' => 'Greece',
            'GS' => 'So. Georgia & So. Sandwich Isl.',
            'GT' => 'Guatemala',
            'GU' => 'Guam',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HK' => 'Hong Kong (China)',
            'HM' => 'Heard & McDonald Islands',
            'HN' => 'Honduras',
            'HR' => 'Croatia',
            'HT' => 'Haiti',
            'HU' => 'Hungary',
            'ID' => 'Indonesia',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IM' => 'Isle of Man',
            'IN' => 'India',
            'IO' => 'British Indian Ocean Territory',
            'IQ' => 'Iraq',
            'IR' => 'Iran',
            'IS' => 'Iceland',
            'IT' => 'Italy',
            'JE' => 'Jersey',
            'JM' => 'Jamaica',
            'JO' => 'Jordan',
            'JP' => 'Japan',
            'KE' => 'Kenya',
            'KG' => 'Kyrgyzstan',
            'KH' => 'Cambodia',
            'KI' => 'Kiribati',
            'KM' => 'Comoros',
            'KN' => 'St. Kitts & Nevis',
            'KP' => 'North Korea',
            'KR' => 'South Korea',
            'KW' => 'Kuwait',
            'KY' => 'Cayman Islands',
            'KZ' => 'Kazakhstan',
            'LA' => 'Laos',
            'LB' => 'Lebanon',
            'LC' => 'St. Lucia',
            'LGBT' => 'Pride',
            'LI' => 'Liechtenstein',
            'LK' => 'Sri Lanka',
            'LR' => 'Liberia',
            'LS' => 'Lesotho',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'LV' => 'Latvia',
            'LY' => 'Libya',
            'MA' => 'Morocco',
            'MC' => 'Monaco',
            'MD' => 'Moldova',
            'ME' => 'Montenegro',
            'MF' => 'St. Martin',
            'MG' => 'Madagascar',
            'MH' => 'Marshall Islands',
            'MK' => 'Macedonia',
            'ML' => 'Mali',
            'MM' => 'Myanmar (Burma)',
            'MN' => 'Mongolia',
            'MO' => 'Macau (China)',
            'MP' => 'Northern Mariana Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MS' => 'Montserrat',
            'MT' => 'Malta',
            'MU' => 'Mauritius',
            'MV' => 'Maldives',
            'MW' => 'Malawi',
            'MX' => 'Mexico',
            'MY' => 'Malaysia',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NC' => 'New Caledonia',
            'NE' => 'Niger',
            'NF' => 'Norfolk Island',
            'NG' => 'Nigeria',
            'NI' => 'Nicaragua',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'NP' => 'Nepal',
            'NR' => 'Nauru',
            'NU' => 'Niue',
            'NZ' => 'New Zealand',
            'OM' => 'Oman',
            'PA' => 'Panama',
            'PE' => 'Peru',
            'PF' => 'French Polynesia',
            'PG' => 'Papua New Guinea',
            'PH' => 'Philippines',
            'PK' => 'Pakistan',
            'PL' => 'Poland',
            'PM' => 'St. Pierre & Miquelon',
            'PN' => 'Pitcairn Islands',
            'PR' => 'Puerto Rico',
            'PS' => 'Palestinian Territories',
            'PT' => 'Portugal',
            'PW' => 'Palau',
            'PY' => 'Paraguay',
            'QA' => 'Qatar',
            'RE' => 'Réunion',
            'RO' => 'Romania',
            'RS' => 'Serbia',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'SA' => 'Saudi Arabia',
            'SB' => 'Solomon Islands',
            'SC' => 'Seychelles',
            'SD' => 'Sudan',
            'SE' => 'Sweden',
            'SG' => 'Singapore',
            'SH' => 'St. Helena',
            'SI' => 'Slovenia',
            'SJ' => 'Svalbard & Jan Mayen',
            'SK' => 'Slovakia',
            'SL' => 'Sierra Leone',
            'SM' => 'San Marino',
            'SN' => 'Senegal',
            'SO' => 'Somalia',
            'SR' => 'Suriname',
            'SS' => 'South Sudan',
            'ST' => 'São Tomé & Príncipe',
            'SV' => 'El Salvador',
            'SX' => 'Sint Maarten',
            'SY' => 'Syria',
            'SZ' => 'Swaziland',
            'TC' => 'Turks & Caicos Islands',
            'TD' => 'Chad',
            'TF' => 'French Southern Territories',
            'TG' => 'Togo',
            'TH' => 'Thailand',
            'TJ' => 'Tajikistan',
            'TK' => 'Tokelau',
            'TL' => 'Timor-Leste',
            'TM' => 'Turkmenistan',
            'TN' => 'Tunisia',
            'TO' => 'Tonga',
            'TR' => 'Turkey',
            'TT' => 'Trinidad & Tobago',
            'TV' => 'Tuvalu',
            'TW' => 'Taiwan',
            'TZ' => 'Tanzania',
            'UA' => 'Ukraine',
            'UG' => 'Uganda',
            'UM' => 'U.S. Outlying Islands',
            'US' => 'United States',
            'US-CA' => 'California',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VA' => 'Vatican City',
            'VC' => 'St. Vincent & Grenadines',
            'VE' => 'Venezuela',
            'VG' => 'British Virgin Islands',
            'VI' => 'U.S. Virgin Islands',
            'VN' => 'Vietnam',
            'VU' => 'Vanuatu',
            'WF' => 'Wallis & Futuna',
            'WS' => 'Samoa',
            'XK' => 'Kosovo',
            'YE' => 'Yemen',
            'YT' => 'Mayotte',
            'ZA' => 'South Africa',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ]);
    }
}
