<?php
/**
 * @author Laurent DESMONCEAUX <laurent@quinen.net>
 * @created 31/08/2018
 * aller plus loin que https://github.com/quinen/cakephp3-plugin/blob/dev/src/Template/Element/Pages/icons.ctp
 * penser a autoswitcher le style en fonction d ela valeur soumise
 * @version 1.0
 */

namespace QuinenCake\View\Helper;

use Cake\Utility\Hash;

/**
 *
 */
trait FontAwesome5Trait
{
    private $_iconsStyleBrand = [
        "500px",
        "accessible-icon",
        "accusoft",
        "adn",
        "adversal",
        "affiliatetheme",
        "algolia",
        "alipay",
        "amazon",
        "amazon-pay",
        "amilia",
        "android",
        "angellist",
        "angrycreative",
        "android",
        "app-store",
        "app-store-ios",
        "apper",
        "angular",
        "apple",
        "apple-pay",
        "asymmetrik",
        "autoprefixer",
        "avianex",
        "aviato",
        "aws",
        "bandcamp",
        "audible",
        "behance",
        "behance-square",
        "bimobject",
        "bitbucket",
        "bitcoin",
        "bity",
        "black-tie",
        "blackberry",
        "blogger",
        "blogger-b",
        "bluetooth",
        "bluetooth-b",
        "btc",
        "buromobelexperte",
        "buysellads",
        "cc-amazon-pay",
        "cc-amex",
        "cc-apple-pay",
        "cc-diners-club",
        "cc-discover",
        "cc-jcb",
        "cc-mastercard",
        "cc-paypal",
        "cc-stripe",
        "cc-visa",
        "centercode",
        "chrome",
        "cloudscale",
        "cloudsmith",
        "cloudversify",
        "codepen",
        "codiepie",
        "connectdevelop",
        "contao",
        "cpanel",
        "creative-commons",
        "creative-commons-by",
        "creative-commons-nc",
        "creative-commons-nc-eu",
        "creative-commons-nc-jp",
        "creative-commons-nd",
        "creative-commons-pd",
        "creative-commons-pd-alt",
        "creative-commons-remix",
        "creative-commons-sa",
        "creative-commons-sampling",
        "creative-commons-sampling-plus",
        "creative-commons-share",
        "css3",
        "css3-alt",
        "cuttlefish",
        "d-and-d",
        "dashcube",
        "delicious",
        "deploydog",
        "deskpro",
        "deviantart",
        "digg",
        "digital-ocean",
        "discord",
        "discourse",
        "dochub",
        "docker",
        "draft2digital",
        "dribbble",
        "dribbble-square",
        "dropbox",
        "drupal",
        "dyalog",
        "earlybirds",
        "ebay",
        "edge",
        "elementor",
        "ello",
        "ember",
        "empire",
        "envira",
        "erlang",
        "ethereum",
        "etsy",
        "expeditedssl",
        "facebook",
        "facebook-f",
        "facebook-messenger",
        "facebook-square",
        "firefox",
        "first-order",
        "first-order-alt",
        "firstdraft",
        "flickr",
        "flipboard",
        "fly",
        "font-awesome",
        "font-awesome-alt",
        "font-awesome-flag",
        "fonticons",
        "fonticons-fi",
        "fort-awesome",
        "fort-awesome-alt",
        "forumbee",
        "foursquare",
        "free-code-camp",
        "freebsd",
        "fulcrum",
        "galactic-republic",
        "galactic-senate",
        "get-pocket",
        "gg",
        "gg-circle",
        "git",
        "git-square",
        "github",
        "github-alt",
        "github-square",
        "gitkraken",
        "gitlab",
        "gitter",
        "glide",
        "glide-g",
        "gofore",
        "goodreads",
        "goodreads-g",
        "google",
        "google-drive",
        "google-play",
        "google-plus",
        "google-plus-g",
        "google-plus-square",
        "google-wallet",
        "gratipay",
        "grav",
        "gripfire",
        "grunt",
        "gulp",
        "hacker-news",
        "hacker-news-square",
        "hackerrank",
        "js-square",
        "hips",
        "hire-a-helper",
        "hooli",
        "hornbill",
        "hotjar",
        "houzz",
        "html5",
        "hubspot",
        "imdb",
        "instagram",
        "internet-explorer",
        "ioxhost",
        "itunes",
        "itunes-note",
        "java",
        "jedi-order",
        "jenkins",
        "joget",
        "joomla",
        "js",
        "jsfiddle",
        "kaggle",
        "keybase",
        "keycdn",
        "kickstarter",
        "kickstarter-k",
        "korvue",
        "laravel",
        "lastfm",
        "lastfm-square",
        "leanpub",
        "less",
        "line",
        "linkedin",
        "linkedin-in",
        "linode",
        "linux",
        "lyft",
        "magento",
        "mailchimp",
        "mandalorian",
        "markdown",
        "mastodon",
        "maxcdn",
        "medapps",
        "medium",
        "medium-m",
        "medrt",
        "meetup",
        "megaport",
        "microsoft",
        "mix",
        "mixcloud",
        "mizuni",
        "modx",
        "monero",
        "napster",
        "neos",
        "nimblr",
        "nintendo-switch",
        "node",
        "node-js",
        "npm",
        "ns8",
        "nutritionix",
        "odnoklassniki",
        "odnoklassniki-square",
        "old-republic",
        "opencart",
        "openid",
        "opera",
        "optin-monster",
        "osi",
        "page4",
        "pagelines",
        "palfed",
        "patreon",
        "paypal",
        "periscope",
        "phabricator",
        "phoenix-framework",
        "phoenix-squadron",
        "php",
        "pied-piper",
        "pied-piper-alt",
        "pied-piper-hat",
        "pied-piper-pp",
        "pinterest",
        "pinterest-p",
        "pinterest-square",
        "playstation",
        "product-hunt",
        "pushed",
        "python",
        "qq",
        "quinscape",
        "quora",
        "r-project",
        "ravelry",
        "react",
        "readme",
        "rebel",
        "red-river",
        "reddit",
        "reddit-alien",
        "reddit-square",
        "rendact",
        "renren",
        "replyd",
        "researchgate",
        "resolving",
        "rev",
        "rocketchat",
        "rockrms",
        "safari",
        "sass",
        "schlix",
        "scribd",
        "searchengin",
        "sellcast",
        "sellsy",
        "servicestack",
        "shirtsinbulk",
        "shopware",
        "simplybuilt",
        "sistrix",
        "sith",
        "skyatlas",
        "skype",
        "slack",
        "slack-hash",
        "slideshare",
        "snapchat",
        "snapchat-ghost",
        "snapchat-square",
        "soundcloud",
        "speakap",
        "spotify",
        "squarespace",
        "stack-exchange",
        "stack-overflow",
        "staylinked",
        "steam",
        "steam-square",
        "steam-symbol",
        "sticker-mule",
        "strava",
        "stripe",
        "stripe-s",
        "studiovinari",
        "stumbleupon",
        "stumbleupon-circle",
        "superpowers",
        "supple",
        "teamspeak",
        "telegram",
        "telegram-plane",
        "tencent-weibo",
        "the-red-yeti",
        "themeco",
        "themeisle",
        "trade-federation",
        "trello",
        "tripadvisor",
        "tumblr",
        "tumblr-square",
        "twitch",
        "twitter",
        "twitter-square",
        "typo3",
        "uber",
        "uikit",
        "uniregistry",
        "untappd",
        "usb",
        "ussunnah",
        "vaadin",
        "viacoin",
        "viadeo",
        "viadeo-square",
        "viber",
        "vimeo",
        "vimeo-square",
        "vimeo-v",
        "vine",
        "vk",
        "vnv",
        "vuejs",
        "weebly",
        "weibo",
        "weixin",
        "whatsapp",
        "whatsapp-square",
        "whmcs",
        "wikipedia-w",
        "windows",
        "wix",
        "wolf-pack-battalion",
        "wordpress",
        "wordpress-simple",
        "wpbeginner",
        "wpexplorer",
        "wpforms",
        "xbox",
        "xing",
        "xing-square",
        "y-combinator",
        "yahoo",
        "yandex",
        "yandex-international",
        "yelp",
        "yoast",
        "youtube",
        "youtube-square",
        "zhihu"
    ];

    private $_iconsStyleRegular = [
        "address-book",
        "address-card",
        "angry",
        "arrow-alt-circle-down",
        "arrow-alt-circle-left",
        "arrow-alt-circle-right",
        "arrow-alt-circle-up",
        "bell",
        "bell-slash",
        "bookmark",
        "building" // solid is better ?
    ];

    private $_iconRotate = [
        90 => "rotate-90",
        180 => "rotate-180",
        270 => "rotate-270",
        'h' => "flip-horizontal",
        'v' => "flip-vertical"
    ];

    private $_iconSizes = ['xs', 'sm', 'lg', 2, 3, 4, 5, 6, 7, 8, 9, 10];

    private $_iconStackSizes = [1, 2];

    private $_iconStylePrefixes = [
        'solid' => "fas",
        'brand' => "fab",
        'regular' => "far",
        'light' => "fal"
    ];

    public function fa5Stacked($iconBack = null, $iconFront = null, $options = [])
    {
        $options += [
            'size' => 1,
            'stackSize' => [2, 1]
        ];

        $options = $this->addClass($options, 'fa-stack');

        // size
        $size = $this->_getIconSize($options['size']);
        if (!empty($size)) {
            $options = $this->addClass($options, $size);
        }
        unset($options['size']);

        $stackSize = $options['stackSize'];
        unset($options['stackSize']);

        list($iconBackIcon, $iconBackOptions) = $this->getContentOptions($iconBack);
        $iconBackOptions += ['stackSize' => Hash::get($stackSize, 0)];
        $iBack = $this->fa5($iconBackIcon, $iconBackOptions);

        list($iconFrontIcon, $iconFrontOptions) = $this->getContentOptions($iconFront);
        $iconFrontOptions += ['stackSize' => Hash::get($stackSize, 1)];
        $iFront = $this->fa5($iconFrontIcon, $iconFrontOptions);

        return $this->tag('span', $iBack . $iFront, $options);
    }

    private function _getIconSize($size)
    {
        $classSize = null;
        if (in_array($size, $this->_iconSizes)) {
            if (\is_numeric($size)) {
                $size .= "x";
            }
            $classSize = "fa-" . $size;
        }

        return $classSize;
    }

    public function fa5($name, $options = [])
    {
        // n icons
        $nameExploded = explode(' ', $name);
        if (count($nameExploded) > 1) {
            return implode(collection($nameExploded)->map(function ($icon) use ($options) {
                return $this->fa5($icon, $options);
            })->toArray());
        }

        if (($pos = strpos($name, '{')) && $pos) {
            $nameOptions = json_decode(substr($name, $pos), true);
            if (($jsonError = (json_last_error())) && $jsonError) {
                return json_last_error_msg() . ' for icon : ' . $name;
            }
            $options = $nameOptions + $options;
            $name = substr($name, 0, $pos);
        }


        $options += [
            'type' => false,
            'size' => false,
            'isFixedWidth' => true,
            'rotate' => false,
            'isSpin' => false,
            'isInverse' => false,
            'stackSize' => false
        ];

        // style
        $options = $this->addClass($options, $this->_getIconStylePrefix($options['type'], $name));
        unset($options['type']);

        // create icon
        $options = $this->addClass($options, 'fa-' . $name);

        // size
        $size = $this->_getIconSize($options['size']);
        if (!empty($size)) {
            $options = $this->addClass($options, $size);
        }
        unset($options['size']);

        // stack size
        $stackSize = $this->_getIconStackSize($options['stackSize']);
        if (!empty($stackSize)) {
            $options = $this->addClass($options, $stackSize);
        }
        unset($options['stackSize']);

        // fixed width
        if ($options['isFixedWidth']) {
            $options = $this->addClass($options, "fa-fw");
            unset($options['isFixedWidth']);
        }

        // rotate
        if ($options['rotate']) {
            $options = $this->addClass($options, $this->_getIconRotate($options['rotate']));
            unset($options['rotate']);
        }

        // spin
        if ($options['isSpin']) {
            $options = $this->addClass($options, "fa-spin");
            unset($options['isSpin']);
        }

        // inverse
        if ($options['isInverse']) {
            $options = $this->addClass($options, "fa-inverse");
            unset($options['isInverse']);
        }

        return $this->tag('i', "", $options);
    }

    private function _getIconStylePrefix($style, $name = false)
    {
        if ($style) {
            $style = $this->_iconStylePrefixes[$style];
        } else {
            if ($name && in_array($name, $this->_iconsStyleBrand)) {
                $style = $this->_iconStylePrefixes['brand'];
            } else {
                if ($name && in_array($name, $this->_iconsStyleRegular)) {
                    $style = $this->_iconStylePrefixes['regular'];
                } else {
                    $style = $this->_iconStylePrefixes['solid'];
                }
            }
        }

        return $style;
    }

    private function _getIconStackSize($size)
    {
        $classSize = null;
        if (in_array($size, $this->_iconStackSizes)) {
            if (\is_numeric($size)) {
                $size .= "x";
            }
            $classSize = "fa-stack-" . $size;
        }

        return $classSize;
    }

    private function _getIconRotate($rotateKey)
    {
        $rotate = false;
        if (in_array($rotateKey, array_keys($this->_iconRotate))) {
            return "fa-" . $this->_iconRotate[$rotateKey];
        }
        return $rotate;
    }
}
