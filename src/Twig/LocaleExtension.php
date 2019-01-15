<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig_Environment;

class LocaleExtension extends AbstractExtension
{
    /** @var Collection */
    private $localeCodes;

    /** @var Collection */
    private $locales;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(string $locales, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->localeCodes = collect(explode('|', $locales));
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('__', [$this, 'translate'], ['is_safe' => ['html']]),
            new TwigFunction('htmllang', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('locales', [$this, 'getLocales'], $env),
            new TwigFunction('contentlocales', [$this, 'getContentLocales'], $env),
            new TwigFunction('locale', [$this, 'getLocale']),
            new TwigFunction('flag', [$this, 'flag'], $safe),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }

    public function translate(string $id, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function getLocale($localeCode)
    {
        return $this->localeInfo($localeCode);
    }

    /**
     * Takes the list of codes of the locales (languages) enabled in the
     * application and returns an array with the name of each locale written
     * in its own language (e.g. English, Français, Español, etc.).
     */
    public function getLocales(Twig_Environment $env): Collection
    {
        if ($this->locales !== null) {
            return $this->locales;
        }

        $this->locales = $this->localeHelper($env, $this->localeCodes);

        return $this->locales;
    }

    public function getContentLocales(Twig_Environment $env, Collection $localeCodes)
    {
        return $this->localeHelper($env, $localeCodes);
    }

    private function localeHelper(Twig_Environment $env, Collection $localeCodes)
    {
        // Get the route and route params, to set the new localized link
        $globals = $env->getGlobals();

        /** @var Request $request */
        $request = $globals['app']->getRequest();
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');

        $locales = new Collection();
        foreach ($localeCodes as $localeCode) {
            $locale = $this->localeInfo($localeCode);
            $routeParams['locale'] = $locale->get('code');
            $locale->put('link', $this->urlGenerator->generate($route, $routeParams));

            $locales->push($locale);
        }

        return $locales;
    }

    public function flag($localeCode)
    {
        $locale = $this->localeInfo($localeCode);

        return sprintf(
            '<span class="fp mr-1 %s" title="%s - %s / %s"></span>',
            $locale->get('flag'),
            $locale->get('name'),
            $locale->get('localizedname'),
            $locale->get('code')
        );
    }

    private function localeInfo($localeCode)
    {
        $splitCode = preg_split('/[_-]/', $localeCode);

        if (isset($splitCode[1])) {
            $localeCode = sprintf('%s_%s', mb_strtolower($splitCode[0]), mb_strtoupper($splitCode[1]));
        } else {
            $localeCode = mb_strtolower($splitCode[0]);
        }

        return collect([
            'code' => $localeCode,
            'name' => Intl::getLocaleBundle()->getLocaleName($localeCode),
            'localizedname' => Intl::getLocaleBundle()->getLocaleName($localeCode, $localeCode),
            'flag' => $this->getFlagTag($localeCode),
        ]);
    }

    private function getFlagTag($localeCode)
    {
        $splitCode = preg_split('/[_-]/', $localeCode);
        $flagcodes = $this->getFlagCodes();

        if (isset($splitCode[1]) && $flagcodes->get($splitCode[1])) {
            return $this->flagImage($splitCode[1]);
        }

        if ($flagcodes->get(mb_strtoupper($splitCode[0]))) {
            return $this->flagImage($splitCode[0]);
        }

        if ($localeCode === 'en') {
            return $this->flagImage('gb');
        }

        return $this->flagImage('blank');
    }

    private function flagImage($flag)
    {
        return sprintf(mb_strtolower($flag));
    }

    private function getFlagCodes()
    {
        return collect([
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
