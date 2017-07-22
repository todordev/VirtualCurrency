<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Version
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency;

/**
 * This class contains data about the extension and methods,
 * which are used for generating information about the version of the extension.
 *
 * @package      Virtualcurrency
 * @subpackage   Version
 */
class Version
{
    /**
     * Extension name
     *
     * @var string
     */
    public $product = 'Virtual Currency';

    /**
     * Main Release Level
     *
     * @var integer
     */
    public $release = '3';

    /**
     * Sub Release Level
     *
     * @var integer
     */
    public $devLevel = '0';

    /**
     * Release Type
     *
     * @var integer
     */
    public $releaseType = 'Pro';

    /**
     * Development Status
     *
     * @var string
     */
    public $devStatus = 'Stable';

    /**
     * Date
     *
     * @var string
     */
    public $releaseDate = '22 July, 2017';

    /**
     * Link to license page.
     *
     * @var string
     */
    public $license = '<a href="http://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">GNU/GPLv3</a>';

    /**
     * Copyright Text
     *
     * @var string
     */
    public $copyright = '&copy; 2017 ITPrism. All rights reserved.';

    /**
     * URL to the extension page.
     *
     * @var string
     */
    public $url = '<a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/virtual-currency-accounts-manager" target="_blank">Virtual Currency</a>';

    /**
     * Backlink to the extension page.
     *
     * @var string
     */
    public $backlink = '<div style="width:100%; text-align: left; font-size: xx-small; margin-top: 10px;"><a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/virtual-currency-accounts-manager" target="_blank">Joomla! Virtual Currency</a></div>';

    /**
     * Developer
     *
     * @var string
     */
    public $developer = '<a href="http://itprism.com" target="_blank">ITPrism</a>';

    /**
     * Minimum required version of Prism library.
     *
     * @var string
     */
    public $requiredPrismVersion = '1.20';

    /**
     *  Build long format of the version text.
     *
     * @return string Long format version
     */
    public function getLongVersion()
    {
        return
            $this->product . ' ' . $this->release . '.' . $this->devLevel . ' ' .
            $this->devStatus . ' ' . $this->releaseDate;
    }

    /**
     *  Build medium format of the version text.
     *
     * @return string Medium format version
     */
    public function getMediumVersion()
    {
        return
            $this->release . '.' . $this->devLevel . ' ' .
            $this->releaseType . ' ( ' . $this->devStatus . ' )';
    }

    /**
     * Build short format of the version text.
     *
     * @return string Short version format.
     */
    public function getShortVersion()
    {
        return $this->release . '.' . $this->devLevel;
    }
}
