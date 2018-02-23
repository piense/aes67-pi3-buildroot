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

 $path = '/var/www/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
 
require_once 'Net/Wifi/Cell.php';
require_once 'Net/Wifi/Config.php';


/**
* A class for scanning wireless networks and identifying
* local wireless network interfaces.
*
* @category Networking
* @package  Net_Wifi
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @link     http://pear.php.net/package/Net_Wifi
*/
class Net_Wifi
{
    var $REG_ACCESS_POINT         = '/Access Point: ([0-9:A-F]{17})/';
    var $REG_BIT_RATE             = '/Bit Rate[:=]([0-9.]+) [mk]b\\/s/i';
    var $REG_ESSID                = '/ESSID:"([^"]+)"/';
    var $REG_INVALID_MISC         = '/Invalid misc[:=](-?[0-9]+)/';
    var $REG_MISSED_BEACON        = '/Missed beacon[:=](-?[0-9]+)/';
    var $REG_NICKNAME             = '/Nickname:"([^"]+)"/';
    var $REG_NOISE_LEVEL          = '/Noise level[:=](-?[0-9]+) dBm/';
    var $REG_POWER                = '/Power[:=]([0-9]+) dBm/';
    var $REG_PROTOCOL_1           = '/IEEE ([0-9.]+[a-z])/';
    var $REG_PROTOCOL_2           = '/([0-9.]+[a-z])\s+linked\s+ESSID/';
    var $REG_RATES                = '|([0-9.]+) Mb/s|';
    var $REG_GROUP_CIPHER         = '|Group Cipher : (.*)|';
    var $REG_PAIRWISE_CIPHERS     = '|Pairwise Ciphers \([0-9]+\) : (.*)|';
    var $REG_AUTH_SUITES          = '|Authentication Suites \([0-9]+\) : (.*)|';
    var $REG_RX_INVALID_CRYPT     = '/Rx invalid crypt[:=](-?[0-9]+)/';
    var $REG_RX_INVALID_FRAG      = '/Rx invalid frag[:=](-?[0-9]+)/';
    var $REG_RX_INVALID_NWID      = '/Rx invalid nwid[:=](-?[0-9]+)/';
    var $REG_SIGNAL_LEVEL         = '/Signal level[:=](-?[0-9]+) dBm/';
    var $REG_TX_EXCESSIVE_RETRIES = '/Tx excessive retries[:=](-?[0-9]+)/';
    var $REG_WPA_IE_STRING        = 'WPA Version 1';
    var $REG_WPA2_IE_STRING       = 'IEEE 802.11i/WPA2 Version 1';

    /**
     * Various locations of programs
     *
     * @var array
     */
    var $arFileLocation = array(
        'iwconfig'           => '/sbin/iwconfig',
        'iwlist'             => '/sbin/iwlist',
        '/proc/net/wireless' => '/proc/net/wireless'
    );

    /**
     * How to handle unknown lines in iwconfig output
     * - 'echo':  Echo to stderr
     * - $object: Log to object with debug priority.
     *            assume 'Log' instance
     * - null:    Ignore unknowns
     *
     * @var string
     */
    var $unknowns = null;



    /**
    * Constructor which tries to guess the paths of the tools
    */
    function __construct()
    {
        //try to find the paths
        $iwconfig = exec('which iwconfig');
        if ($iwconfig !== false) {
            $this->setPathIwconfig($iwconfig);
        } else if (file_exists('/sbin/iwconfig')) {
            $this->setPathIwconfig('/sbin/iwconfig');
        }

        $iwlist = exec('which iwlist');
        if ($iwlist !== false) {
            $this->setPathIwlist($iwlist);
        } else if (file_exists('/sbin/iwlist')) {
            $this->setPathIwlist('/sbin/iwlist');
        }
    }

    /**
     * PHP 4 constructor for backwards compatibility.
     *
     * @return void
     */
    function Net_Wifi()
    {
        self::__construct();
    }


    /**
    * Returns an object with the current state of the interface
    * (connected/not connected, AP,...).
    *
    * @param string $strInterface The interface to check
    *
    * @return Net_Wifi_Config The state information
    * @access public
    */
    function getCurrentConfig($strInterface)
    {
        //get the plain config
        $arLines = array();
        exec(
            $this->arFileLocation['iwconfig'] . ' '
            . escapeshellarg($strInterface),
            $arLines
        );
        $strAll = implode("\n", $arLines);

        return $this->parseCurrentConfig($strAll);
    }//function getCurrentConfig(..)



    /**
    * Parses the iwconfig output to collect the current config information.
    *
    * @param string $strAll The iwconfig output to parse
    *
    * @return Net_Wifi_Config  The current config object
    * @access protected
    */
    function parseCurrentConfig($strAll)
    {
        $objConfig = new Net_Wifi_Config();

        $arMatches = array();
        if (preg_match($this->REG_ESSID, $strAll, $arMatches)) {
            $objConfig->ssid = $arMatches[1];
        }
        if (preg_match($this->REG_ACCESS_POINT, $strAll, $arMatches)) {
            $objConfig->ap = $arMatches[1];
        }
        if (preg_match($this->REG_NICKNAME, $strAll, $arMatches)) {
            $objConfig->nick = $arMatches[1];
        }
        if (strpos($strAll, 'Mode:Managed')) {
            $objConfig->mode = 'managed';
        } else if (strpos($strAll, 'Mode:Ad-Hoc')) {
            $objConfig->mode = 'ad-hoc';
        }
        if (preg_match($this->REG_BIT_RATE, $strAll, $arMatches)) {
            $objConfig->rate = $arMatches[1];
        }
        if (preg_match($this->REG_POWER, $strAll, $arMatches)) {
            $objConfig->power = $arMatches[1];
        }
        if (preg_match($this->REG_SIGNAL_LEVEL, $strAll, $arMatches)) {
            $objConfig->rssi = $arMatches[1];
        }
        if (preg_match($this->REG_NOISE_LEVEL, $strAll, $arMatches)) {
            $objConfig->noise = $arMatches[1];
        }
        if (preg_match($this->REG_PROTOCOL_1, $strAll, $arMatches)) {
            $objConfig->protocol = $arMatches[1];
        } elseif (preg_match($this->REG_PROTOCOL_2, $strAll, $arMatches)) {
            $objConfig->protocol = $arMatches[1];
        }

        if (preg_match($this->REG_RX_INVALID_NWID, $strAll, $arMatches)) {
            $objConfig->packages_rx_invalid_nwid = $arMatches[1];
        }
        if (preg_match($this->REG_RX_INVALID_CRYPT, $strAll, $arMatches)) {
            $objConfig->packages_rx_invalid_crypt = $arMatches[1];
        }
        if (preg_match($this->REG_RX_INVALID_FRAG, $strAll, $arMatches)) {
            $objConfig->packages_rx_invalid_frag = $arMatches[1];
        }
        if (preg_match($this->REG_TX_EXCESSIVE_RETRIES, $strAll, $arMatches)) {
            $objConfig->packages_tx_excessive_retries = $arMatches[1];
        }
        if (preg_match($this->REG_INVALID_MISC, $strAll, $arMatches)) {
            $objConfig->packages_invalid_misc = $arMatches[1];
        }
        if (preg_match($this->REG_MISSED_BEACON, $strAll, $arMatches)) {
            $objConfig->packages_missed_beacon = $arMatches[1];
        }

        //available in ipw2200 1.0.3 only
        if (strpos($strAll, 'radio off')) {
            $objConfig->activated = false;
        }

        if (strpos($strAll, 'unassociated') === false
            && $objConfig->ap != null && $objConfig->ap != '00:00:00:00:00:00'
        ) {
            $objConfig->associated = true;
        }

        return $objConfig;
    }//function parseCurrentConfig(..)



    /**
    * Checks if a network interface is connected to an access point.
    *
    * @param string $strInterface The network interface to check
    *
    * @return boolean If the interface is connected
    * @access public
    */
    function isConnected($strInterface)
    {
        $objConfig = $this->getCurrentConfig($strInterface);

        return $objConfig->associated;
    }//function isConnected(..)



    /**
    * Returns an array with the names/device files of
    *  all supported wireless lan devices.
    *
    * @access public
    * @return array   Array with wireless interfaces as values
    */
    function getSupportedInterfaces()
    {
        $arWirelessInterfaces = array();
        if (is_executable($this->arFileLocation['iwconfig'])) {
            // use iwconfig
            $arLines = array();
            exec($this->arFileLocation['iwconfig'] . ' 2>&1', $arLines);
            foreach ($arLines as $strLine) {
                if (strlen($strLine)
                    && trim($strLine[0]) != ''
                    && strpos($strLine, 'no wireless extensions') === false
                ) {
                    //there is something
                    $arWirelessInterfaces[]
                        = substr($strLine, 0, strpos($strLine, ' '));
                }
            }//foreach line

        } else if (file_exists($this->arFileLocation['/proc/net/wireless'])) {
            // use /proc/net/wireless
            $arLines = file($this->arFileLocation['/proc/net/wireless']);
            //begin with 3rd line
            if (count($arLines) > 2) {
                for ($nA = 2; $nA < count($arLines); $nA++) {
                    $nPos         = strpos($arLines[$nA], ':', 0);
                    $strInterface = trim(substr($arLines[$nA], 0, $nPos));
                    //assign interface
                    $arWirelessInterfaces[] = $strInterface;
                }
            }//we've got more than 2 lines
        }

        return $arWirelessInterfaces;
    }//function getSupportedInterfaces()



    /**
    * Scans for access points / ad hoc cells and returns them.
    *
    * @param string $strInterface The interface to use
    *
    * @return array Array with cell information objects (Net_Wifi_Cell)
    * @access public
    */
    function scan($strInterface)
    {
        $arLines = array();
        exec( 'sudo '.
            $this->arFileLocation['iwlist'] . ' '
            . escapeshellarg($strInterface) . ' scanning'
            . ' 2>&1',
            $arLines
        );

        return $this->parseScan($arLines);
    }//function scan(..)



    /**
    * Parses the output of iwlist and returns the recognized cells.
    *
    * @param array $arLines Lines of the iwlist output as an array
    *
    * @return array Array with cell information objects
    * @access protected
    */
    function parseScan($arLines)
    {
        if (count($arLines) == 1) {
            //one line only -> no cells there
            return array();
        }

        //if bit rates are alone on lines
        $bStandaloneRates = false;

        //split into cells
        $arCells      = array();
        $nCurrentCell = -1;
        $nCount       = count($arLines);
        for ($nA = 1; $nA < $nCount; $nA++) {
            $strLine = trim($arLines[$nA]);
            if ($strLine == '') {
                continue;
            }

            if (substr($strLine, 0, 4) == 'Cell') {
                //we've got a new cell
                $nCurrentCell++;
                //get cell number
                $nCell = substr($strLine, 5, strpos($strLine, ' ', 5) - 5);
                //add new cell
                $arCells[$nCurrentCell]       = new Net_Wifi_Cell();
                $arCells[$nCurrentCell]->cell = $nCell;
                $arCells[$nCurrentCell]->ies = array();
                $arCells[$nCurrentCell]->wpa = false;
                $arCells[$nCurrentCell]->wpa2 = false;
                $arCells[$nCurrentCell]->wpa_group_cipher = array();
                $arCells[$nCurrentCell]->wpa_pairwise_cipher = array();
                $arCells[$nCurrentCell]->wpa_auth_suite = array();
                $arCells[$nCurrentCell]->wpa2_group_cipher = array();
                $arCells[$nCurrentCell]->wpa2_pairwise_cipher = array();
                $arCells[$nCurrentCell]->wpa2_auth_suite = array();

                //remove cell information from line for further interpreting
                $strLine = substr($strLine, strpos($strLine, '- ') + 2);
            }

            $nPos       = strpos($strLine, ':');
            $nPosEquals = strpos($strLine, '=');
            if ($nPosEquals !== false && ($nPos === false || $nPosEquals < $nPos)) {
                //sometimes there is a "=" instead of a ":"
                $nPos = $nPosEquals;
            }
            $nPos++;

            $strId    = strtolower(substr($strLine, 0, $nPos - 1));
            $strValue = trim(substr($strLine, $nPos));
            switch ($strId) {
            case 'ie':
                if ($strValue == $this->REG_WPA_IE_STRING) {
                    // WPA1: "WPA Version 1"
                    // (multiline with Group Cipher list,
                    //  Pairwise Ciphers list and Authentication Suites)
                    /*
                    * WPA Version 1
                    *     Group Cipher : TKIP
                    *     Pairwise Ciphers (2) : TKIP CCMP
                    *     Authentication Suites (1) : PSK
                    */
                    $arCells[$nCurrentCell]->wpa = true;
                    $bStandaloneRates = true;
                }

                if ($strValue == $this->REG_WPA2_IE_STRING) {
                    // WPA2: "IEEE 802.11i/WPA2 Version 1"
                    // (multiline with Group Cipher list,
                    //  Pairwise Ciphers list and Authentication Suites)
                    /*
                    * IEEE 802.11i/WPA2 Version 1
                    *     Group Cipher : CCMP
                    *     Pairwise Ciphers (1) : CCMP
                    *     Authentication Suites (1) : PSK
                    */
                    $arCells[$nCurrentCell]->wpa2 = true;
                    $bStandaloneRates = true;
                }
                $arCells[$nCurrentCell]->ies[] = $strValue;
                $arLines[$nA]     = $strValue;
                $nA--;//go back one so that this line is re-parsed
                break;

            case 'address':
                $arCells[$nCurrentCell]->mac = $strValue;
                break;

            case 'essid':
                if ($strValue[0] == '"') {
                    //has quotes around
                    $arCells[$nCurrentCell]->ssid = substr($strValue, 1, -1);
                } else {
                    $arCells[$nCurrentCell]->ssid = $strValue;
                }
                break;

            case 'bit rate':
                $nRate = floatval(substr($strValue, 0, strpos($strValue, 'Mb/s')));
                //assign rate.
                $arCells[$nCurrentCell]->rate    = $nRate;
                $arCells[$nCurrentCell]->rates[] = $nRate;
                break;

            case 'bit rates':
                $bStandaloneRates = true;
                $arLines[$nA]     = $strValue;
                $nA--;//go back one so that this line is re-parsed
                break;

            case 'protocol':
                if (substr($strValue, 0, 5) == 'IEEE ') {
                    $strValue = substr($strValue, 5);
                }
                $arCells[$nCurrentCell]->protocol = $strValue;
                break;

            case 'channel':
                $arCells[$nCurrentCell]->channel = intval($strValue);
                break;

            case 'encryption key':
                if ($strValue == 'on') {
                    $arCells[$nCurrentCell]->encryption = true;
                } else {
                    $arCells[$nCurrentCell]->encryption = false;
                }
                break;

            case 'mode':
                if (strtolower($strValue) == 'master') {
                    $arCells[$nCurrentCell]->mode = 'master';
                } else {
                    $arCells[$nCurrentCell]->mode = 'ad-hoc';
                }
                break;

            case 'signal level':
                $arCells[$nCurrentCell]->rssi
                    = substr($strValue, 0, strpos($strValue, ' '));
                break;

            case 'quality':
                $arData                          = explode('  ', $strValue);
                $arCells[$nCurrentCell]->quality = $arData[0];
                if (trim($arData[1]) != '') {
                    //bad hack
                    $arLines[$nA] = $arData[1];
                    $nA--;
                    if (isset($arData[2])) {
                        $arLines[$nA - 1] = $arData[1];
                        $nA--;
                    }
                }
                break;

            case 'frequency':
                $match = preg_match(
                    '/([0-9.]+ GHz) \(Channel ([0-9])\)/',
                    $strValue, $arMatches
                );
                if ($match) {
                    $arCells[$nCurrentCell]->frequency = $arMatches[1];
                    $arCells[$nCurrentCell]->channel   = $arMatches[2];
                } else {
                    $arCells[$nCurrentCell]->frequency = $strValue;
                }
                break;

            case 'extra':
                $nPos     = strpos($strValue, ':');
                $strSubId = strtolower(trim(substr($strValue, 0, $nPos)));
                $strValue = trim(substr($strValue, $nPos + 1));
                switch ($strSubId) {
                case 'rates (mb/s)':
                    //1 2 5.5 11 54
                    $arRates = explode(' ', $strValue);
                    //convert to float values
                    foreach ($arRates as $nB => $strRate) {
                        $arCells[$nCurrentCell]->rates[$nB] = floatval($strRate);
                    }
                    break;

                case 'signal':
                case 'rssi':
                    //-53 dBm
                    $arCells[$nCurrentCell]->rssi
                        = intval(substr($strValue, 0, strpos($strValue, ' ')));
                    break;

                case 'last beacon':
                    //25ms ago
                    $arCells[$nCurrentCell]->beacon
                        = intval(substr($strValue, 0, strpos($strValue, 'ms')));
                    break;

                default:
                    $this->handleUnknown(null, $strSubId);
                    break;
                }
                break;

            default:
                if ($bStandaloneRates) {
                    if (preg_match_all($this->REG_RATES, $strLine, $arMatches) > 0) {
                        foreach ($arMatches[1] as $nRate) {
                            $nRate                           = floatval($nRate);
                            $arCells[$nCurrentCell]->rate    = $nRate;
                            $arCells[$nCurrentCell]->rates[] = $nRate;
                        }
                        break;
                    }

                    $found = $this->parseWpaCipher(
                        $strLine, $arCells, $nCurrentCell,
                        $this->REG_GROUP_CIPHER, 'group_cipher'
                    );
                    if ($found) {
                        break;
                    }

                    $found = $this->parseWpaCipher(
                        $strLine, $arCells, $nCurrentCell,
                        $this->REG_PAIRWISE_CIPHERS, 'pairwise_cipher'
                    );
                    if ($found) {
                        break;
                    }

                    $found = $this->parseWpaCipher(
                        $strLine, $arCells, $nCurrentCell,
                        $this->REG_AUTH_SUITES, 'auth_suite'
                    );
                    if ($found) {
                        break;
                    }
                }
                $this->handleUnknown($strId, null);
                break;
            }
        }//foreach line


        //not all outputs are sorted (note the 6)
        //Extra: Rates (Mb/s): 1 2 5.5 9 11 6 12 18 24 36 48 54
        //additionally, some drivers have many single "Bit Rate:"
        // fields instead of one big one
        foreach ($arCells as $nCurrentCell => $arData) {
            sort($arCells[$nCurrentCell]->rates);
            $arCells[$nCurrentCell]->rates
                = array_unique($arCells[$nCurrentCell]->rates);
        }

        return $arCells;
    }//function parseScan(..)

    /**
     * Parse a WPA/WPA2 cipher string and append it to the cell
     *
     * @param string  $strLine      Input line that gets parsed
     * @param array   $arCells      Array of cell data
     * @param integer $nCurrentCell Key of current cell in $arCells
     * @param string  $regex        Expression to match line against
     * @param string  $property     Cell property to set (without wpa_/wpa2_)
     *
     * @return boolean True if a cipher matched
     */
    protected function parseWpaCipher(
        $strLine, &$arCells, $nCurrentCell, $regex, $property
    ) {
        if (preg_match_all($regex, $strLine, $arMatches) == 0) {
            return false;
        }
        foreach ($arMatches[1] as $nCipher) {
            //WPA 1
            if (end($arCells[$nCurrentCell]->ies) == $this->REG_WPA_IE_STRING) {
                $arCells[$nCurrentCell]->{'wpa_' . $property}
                    = explode(' ', $nCipher);
            }
            //WPA 2
            if (end($arCells[$nCurrentCell]->ies) == $this->REG_WPA2_IE_STRING) {
                $arCells[$nCurrentCell]->{'wpa2_' . $property}
                    = explode(' ', $nCipher);
            }
        }
        return true;
    }

    /**
    * Tells the driver to use the access point with the given MAC address only.
    *
    * You can use "off" to enable automatic mode again without
    * changing the current AP, or "any" resp. "auto" to force
    * the card to re-associate with the currently best AP
    *
    * EXPERIMENTAL! WILL CHANGE IN FUTURE VERSIONS
    *
    * @param string $strInterface The interface to use
    * @param string $strMac       The mac address of the access point
    *
    * @return boolean True if setting was ok, false if not
    * @access public
    */
    function connectToAccessPoint($strInterface, $strMac)
    {
        $arLines    = array();
        $nReturnVar = 0;
        exec(
            $this->arFileLocation['iwconfig'] . ' ' . escapeshellarg($strInterface)
            . ' ap ' . escapeshellarg($strMac),
            $arLines,
            $nReturnVar
        );

        return $nReturnVar == 0;
    }//function connectToAccessPoint(..)



    /**
     * Handle unknown configuration lines.
     *
     * @param string $strId    iwconfig output prefix (i.e. 'frequency')
     * @param string $strSubId iwconfig output value
     *
     * @return void
     *
     * @uses $unknowns
     */
    function handleUnknown($strId, $strSubId)
    {
        if ($this->unknowns === null) {
            return;
        }

        if ($strId !== null) {
            $strMsg = 'unknown iwconfig information: ' . $strId;
        } else {
            $strMsg = 'unknown iwconfig extra information: ' . $strSubId;
        }

        if ($this->unknowns == 'echo') {
            fwrite(STDERR, $strMsg . "\r\n");
        } else if (is_object($this->unknowns)) {
            $this->unknowns->debug($strMsg);
        }
    }//function handleUnknown(..)



    /*
     * and now some dumb getters and setters
     */



    /**
    * Returns the set path to /proc/wireless.
    *
    * @return string The path to "/proc/net/wireless"
    * @access public
    */
    function getPathProcWireless()
    {
        return $this->arFileLocation['/proc/net/wireless'];
    }//function getPathProcWireless()



    /**
    * Set the path to /proc/net/wireless.
    *
    * @param string $strProcWireless The new /proc/net/wireless path
    *
    * @return null
    * @access public
    */
    function setPathProcWireless($strProcWireless)
    {
        $this->arFileLocation['/proc/net/wireless'] = $strProcWireless;
    }//function setPathProcWireless(..)



    /**
    * Returns the set path to iwconfig.
    *
    * @return string The path to iwconfig
    * @access public
    */
    function getPathIwconfig()
    {
        return $this->arFileLocation['iwconfig'];
    }//function getPathIwconfig()



    /**
    * Set the path to iwconfig.
    *
    * @param string $strPathIwconfig The new ifwconfig path
    *
    * @return null
    * @access public
    */
    function setPathIwconfig($strPathIwconfig)
    {
        $this->arFileLocation['iwconfig'] = $strPathIwconfig;
    }//function setPathIwconfig(..)



    /**
    * Returns the set path to iwlist.
    *
    * @return string The path to iwlist
    *
    * @access public
    */
    function getPathIwlist()
    {
        return $this->arFileLocation['iwlist'];
    }//function getPathIwlist()



    /**
    * Returns the set path to iwlist.
    *
    * @param string $strPathIwlist The new iwlist path
    *
    * @return void
    * @access public
    */
    function setPathIwlist($strPathIwlist)
    {
        $this->arFileLocation['iwlist'] = $strPathIwlist;
    }//function setPathIwlist(..)


}//class Net_Wifi

?>
