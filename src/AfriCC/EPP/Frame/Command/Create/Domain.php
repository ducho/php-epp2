<?php

/**
 * This file is part of the php-epp2 library.
 *
 * (c) Gunter Grodotzki <gunter@afri.cc>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace AfriCC\EPP\Frame\Command\Create;

use AfriCC\EPP\Frame\Command\Create as CreateCommand;
use AfriCC\EPP\Validator;
use AfriCC\EPP\Random;
use Exception;

/**
 * @link http://tools.ietf.org/html/rfc5731#section-3.2.1
 */
class Domain extends CreateCommand
{
    protected $host_attr_index = 0;

    public function setDomain($domain)
    {
        if (!Validator::isHostname($domain)) {
            throw new Exception(sprintf('%s is not a valid domain name', $domain));
        }

        $this->set('domain:name', $domain);
    }

    public function setPeriod($period)
    {
        if (preg_match('/^(\d+)([a-z])$/i', $period, $matches)) {
            $this->set(sprintf('domain:period[@unit=\'%s\']', $matches[2]), $matches[1]);
        } else {
            throw new Exception(sprintf('%s is not a valid period', $period));
        }
    }

    public function addHostObj($host)
    {
        if (!Validator::isHostname($host)) {
            throw new Exception(sprintf('%s is not a valid host name', $host));
        }

        $this->set('domain:ns/domain:hostObj[]', $host);
    }

    public function addHostAttr($host, $ips = null)
    {
        if (!Validator::isHostname($host)) {
            throw new Exception(sprintf('%s is not a valid host name', $host));
        }

        $this->set(sprintf('domain:ns/domain:hostAttr[%d]/domain:hostName', $this->host_attr_index), $host);

        if (!empty($ips) && is_array($ips)) {
            foreach ($ips as $ip) {
                $ip_type = Validator::getIPType($ip);
                if ($ip_type === false) {
                    throw new Exception(sprintf('%s is not a valid IP address', $ip));
                } elseif ($ip_type === Validator::TYPE_IPV4) {
                    $this->set(sprintf('domain:ns/domain:hostAttr[%d]/domain:hostAddr[@ip=\'v4\']', $this->host_attr_index), $ip);
                } elseif ($ip_type === Validator::TYPE_IPV6) {
                    $this->set(sprintf('domain:ns/domain:hostAttr[%d]/domain:hostAddr[@ip=\'v6\']', $this->host_attr_index), $ip);
                }
            }
        }

        ++$this->host_attr_index;
    }

    public function setRegistrant($registrant)
    {
        $this->set('domain:registrant', $registrant);
    }

    public function setAdminContact($admin_contact)
    {
        $this->set('domain:contact[@type=\'admin\']', $admin_contact);
    }

    public function setTechContact($tech_contact)
    {
        $this->set('domain:contact[@type=\'tech\']', $tech_contact);
    }

    public function setAuthInfo($pw = null)
    {
        if ($pw === null) {
            $pw = Random::auth(12);
        }

        $this->set('domain:authInfo/domain:pw', $pw);
    }
}
