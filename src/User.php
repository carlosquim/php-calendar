<?php
/*
 * Copyright Sean Proctor
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PhpCalendar;

class User
{
    /**
     * @var int $uid
     */
    private $uid;
    /**
     * @var string $username
     */
    private $username;
    /**
     * @var string $hash
     */
    private $hash;
    /**
     * @var string $admin
     */
    private $admin;
    /**
     * @var bool $password_editable
     */
    private $password_editable;
    /**
     * @var int $default_cid
     */
    private $default_cid;
    /**
     * @var string|null $timezone
     */
    private $timezone;
    /**
     * @var string|null $language
     */
    private $locale;
    private $groups;
    /**
     * @var bool $disabled
     */
    private $disabled;
    /**
     * @var Database $db
     */
    private $db;

    /**
     * User constructor.
     *
     * @param Database $db
     */
    private function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param Database $db
     * @param $map
     * @return User
     */
    public static function createFromMap(Database $db, $map)
    {
        $user = new User($db);

        $user->uid = $map['uid'];
        $user->username = $map['username'];
        $user->hash = $map['password'];
        $user->admin = $map['admin'];
        $user->password_editable = $map['password_editable'];
        $user->default_cid = $map['default_cid'];
        $user->timezone = $map['timezone'];
        $user->locale = $map['language'];
        $user->disabled = $map['disabled'];

        return $user;
    }

    /**
     * @param Context $context
     * @return User
     */
    public static function createAnonymous(Context $context)
    {
        $user = new User($context->db);

        $user->uid = 0;
        $user->username = 'anonymous';
        $user->admin = false;
        $user->password_editable = false;
        $user->timezone = User::getAnonymousTimezone($context);
        $user->locale = User::getAnonymousLocale($context);
        $user->disabled = false;

        return $user;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->hash;
    }

    /**
     * @return bool
     */
    public function hasEditablePassword()
    {
        return $this->password_editable;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    public function getLocale()
    {
        return $this->locale;
    }
    
    public function getGroups()
    {
        if (!isset($this->groups)) {
            $this->groups = $this->db->getGroupsForUser($this->uid);
        }

        return $this->groups;
    }

    public function isDisabled()
    {
        return $this->disabled;
    }

    public function isAdmin()
    {
        return $this->admin;
    }

    public function defaultCid()
    {
        return $this->default_cid;
    }

    public function isUser()
    {
        return $this->uid > 0;
    }

    /**
     * @param Context $context
     * @return string|null
     */
    private static function getAnonymousTimezone(Context $context)
    {
        $tz = $context->request->get('tz');
        // If we have a timezone, make sure it's valid
        if (in_array($tz, timezone_identifiers_list())) {
            return $tz;
        }
    
        return null;
    }
    
    /**
     * @param Context $context
     * @return string|null
     */
    private static function getAnonymousLocale(Context $context)
    {
        if ($context->request->get('lang') !== null) {
            $lang = $context->request->get('lang');
            $context->session->set('_locale', $lang);
            return $lang;
        } // else
        if ($context->session->get('_locale') !== null) {
            return $context->session->get('_locale');
        } // else
        return null;
    }
}
