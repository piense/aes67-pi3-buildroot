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
* Configuration settings of a wifi network interface.
*
* @category Networking
* @package  Net_Wifi
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @link     http://pear.php.net/package/Net_Wifi
*/
class Net_Wifi_Config
{
    /**
     * If the interface is activated.
     * Some notebooks have a button which deactivates wifi, this is recognized here.
     * Note that this setting can't be read by all drivers, and so
     *  it's "true" if it can't be determined. You can be sure that it's deactivated
     *  if this setting is false, but not that it's activated if it's true
     *
     * @var boolean
     */
    var $activated = true;

    /**
     * MAC address of the associated access point.
     *
     * @var string
     */
    var $ap = null;

    /**
     * If the interface is connected to an access point or an ad-hoc network.
     *
     * @var boolean
     */
    var $associated = false;

    /**
     * Network type.
     * Can be "master" or "ad-hoc" (without quotes)
     *
     * @var string
     */
    var $mode = null;

    /**
     * The nickname which the interface (computer) uses.
     * Something like a computer name
     *
     * @var string
     */
    var $nick = null;

    /**
     * Noise level in dBm - how much the signal is disturbed
     * example: -249
     *
     * @var int
     */
    var $noise = null;

    /**
     * Other packets lost in relation with specific wireless operations.
     *
     * @var int
     */
    var $packages_invalid_misc = 0;

    /**
     * Number of periodic beacons from the Cell or the Access Point we have
     * missed. Beacons are sent at regular intervals to maintain the cell
     * coordination, failure to receive them usually indicates that the card
     * is out of range.
     *
     * @var int
     */
    var $packages_missed_beacon = 0;

    /**
     * Number of packets that the hardware was unable to decrypt.
     * This can be used to detect invalid encryption
     * settings.
     *
     * @var int
     */
    var $packages_rx_invalid_crypt = null;

    /**
     * Number of packets for which the hardware was not able to properly
     * re-assemble the link layer fragments (most likely
     * one was missing).
     *
     * @var int
     */
    var $packages_rx_invalid_frag = null;

    /**
     * Number of packets received with a different NWID or ESSID.
     * Used to detect configuration problems or adjacent
     * network existence (on the same frequency).
     *
     * @var int
     */
    var $packages_rx_invalid_nwid = null;

    /**
     * Number of packages that needed to be re-submitted repeatedly again
     * and again, because no ACK was received for them.
     * You have a bad connection or are connecting long distance.
     *
     * @var int
     */
    var $packages_tx_excessive_retries = null;

    /**
     * Power setting of the interface.
     *
     * @var int
     */
    var $power = null;

    /**
     * Protocol version which is used for connection.
     * example: "IEEE 802.11g" without quotes
     *
     * @var string
     */
    var $protocol = null;

    /**
     * The bit rate of the connection.
     *
     * @var float
     */
    var $rate = null;

    /**
     * Signal strength in dBm.
     * example: -59
     *
     * @var int
     */
    var $rssi = null;

    /**
     * "Service Set IDentifier" of the cell which identifies current network.
     * Max. 32 alphanumeric characters
     * example: "My Network" (without quotes)
     *
     * @var string
     */
    var $ssid = null;

}//class Net_Wifi_Config
?>
