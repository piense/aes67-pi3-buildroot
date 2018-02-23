<?php
/**
* Configuration settings of a wifi network interface.
*
* PHP Versions 4 and 5
*
* @category Networking
* @package  Net_Wifi
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @version  CVS: $Id$
* @link     http://pear.php.net/package/Net_Wifi
*/

/**
* Cell information from a wifi scan.
*
* @category Networking
* @package  Net_Wifi
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @link     http://pear.php.net/package/Net_Wifi
*/
class Net_Wifi_Cell
{
    /**
     * The current cell number.
     * Has nothing to say.
     *
     * @var int
     */
    var $cell = null;

    /**
     * MAC address of the cell (access point or ad-hoc station).
     * example: 00:40:05:28:EB:45
     *
     * @var string
     */
    var $mac = null;

    /**
     * "Service Set IDentifier" of the cell which identifies the network.
     * Max. 32 alphanumeric characters
     * example: "My Network" (without quotes)
     *
     * @var string
     */
    var $ssid = null;

    /**
     * Network type.
     * can be "master" or "ad-hoc" (without quotes)
     *
     * @var string
     */
    var $mode = null;

    /**
     * Channel number used for communication.
     * number from 1 to 12 or so
     *
     * @var int
     */
    var $channel = null;

    /**
     * If encryption is used.
     *
     * @var boolean
     */
    var $encryption = null;

    /**
     * Channel frequency.
     * example: 2.412GHz
     *
     * @var string
     */
    var $frequency = null;

    /**
     * The protocol version used.
     * example: IEEE 802.11b
     *
     * @var string
     */
    var $protocol = null;

    /**
     * Bit rate which is used (when connected).
     * May be set when not connected!
     *
     * @var float
     */
    var $rate = null;

    /**
     * Array of supported bit rates.
     *
     * @var array(float)
     */
    var $rates = array();

    /**
     * Signal strength.
     * example: -59
     *
     * @var int
     */
    var $rssi = null;

    /**
     * The time since the last beacon (time sync) frame has been sent, in ms.
     *
     * @var int
     */
    var $beacon = null;

}

?>
