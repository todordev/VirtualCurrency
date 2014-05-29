<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing e-mail templates.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyEmail
{
    protected $id;
    protected $title;
    protected $subject;
    protected $body;
    protected $senderName;
    protected $senderEmail;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    protected $replaceable = array(
        "{SITE_NAME}",
        "{SITE_URL}",
        "{ITEM_TITLE}",
        "{ITEM_URL}",
        "{SENDER_NAME}",
        "{SENDER_EMAIL}",
        "{RECIPIENT_NAME}",
        "{RECIPIENT_EMAIL}",
        "{AMOUNT}",
        "{UNITS}",
        "{UNITS_TITLE}",
        "{TRANSACTION_ID}",
    );

    /**
     * Initialize the object.
     *
     * <code>
     * $subject  = "My e-mail subject...";
     * $body     = "My e-mail body...";
     * $email    = new VirtualCurrencyEmail($subject, $body);
     * </code>
     *
     * @param string $subject
     * @param string $body
     */
    public function __construct($subject = "", $body = "")
    {
        $this->subject = $subject;
        $this->body    = $body;
    }

    /**
     * Set the database object.
     *
     * <code>
     * $email    = new VirtualCurrencyEmail();
     * $email->setDb(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Load an email data from database.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     * </code>
     *
     * @param int $id  The ID of the e-mail template.
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.subject, a.body, a.sender_name, a.sender_email")
            ->from($this->db->quoteName("#__vc_emails", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Set data to object properties.
     *
     * <code>
     * $data = array(
     *  "subject" => "My e-mail subject...",
     *  "body" =>"My e-mail body..."
     * );
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return an ID of an email template.
     *
     * <code>
     * $emailId = 1;
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * if (!$email->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set a subject of an email template.
     *
     * <code>
     * $subject = "My Subject...";
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setSubject($subject);
     * </code>
     *
     * @param string $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = strip_tags($subject);

        return $this;
    }

    /**
     * Return a subject of an email template.
     *
     * <code>
     * $emailId = 1;
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setDb($emailId);
     * $email->load();
     *
     * $subject = $email->getSubject();
     * </code>
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set a body of an email template.
     *
     * <code>
     * $body = "My body...";
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setBody($body);
     * </code>
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Return body of the message.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $body    = $item->getBody("plain");
     * </code>
     *
     * @param string $mode Mail type - html or plain ( plain text ).
     *
     * @return string
     */
    public function getBody($mode = "html")
    {
        $mode = JString::strtolower($mode);
        if (strcmp("plain", $mode) == 0) {
            $body = str_replace("<br />", "\n", $this->body);
            $body = strip_tags($body);

            return $body;
        } else {
            return $this->body;
        }
    }

    /**
     * Set a sender name of an email template.
     *
     * <code>
     * $senderName = "John Dow";
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setSenderName($senderName);
     * </code>
     *
     * @param string $name
     *
     * @return self
     */
    public function setSenderName($name)
    {
        $this->senderName = $name;

        return $this;
    }

    /**
     * Return the name of the sender for current email template.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $senderName  = $item->getSenderName();
     * </code>
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set a sender email of an email template.
     *
     * <code>
     * $senderEmail = "john@dow.com";
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setSenderEmail($senderEmail);
     * </code>
     *
     * @param string $email
     *
     * @return self
     */
    public function setSenderEmail($email)
    {
        $this->senderEmail = $email;

        return $this;
    }

    /**
     * Return the e-mail of the sender for current email template.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VirtualCurrencyEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $senderEmail  = $item->getSenderEmail();
     * </code>
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Parse subject and body, replacing indicators with other values.
     *
     * <code>
     * $subject = "Here you are my website.";
     * $body = "My website is {WEBSITE}...";
     *
     * $data = array(
     *  "website" => "http://itprism.com"
     * );
     *
     * $email    = new VirtualCurrencyEmail($subject, $body);
     * $email->bind($data);
     *
     * // Replace {WEBSITE} with http://itprism.com.
     * $email->parse($data);
     *
     * $body = $email->setBody();
     * </code>
     *
     * @param array $data
     *
     * @return string
     */
    public function parse($data)
    {
        foreach ($data as $key => $value) {

            // Prepare flag
            $search = "{" . JString::strtoupper($key) . "}";

            // Parse subject
            $this->subject = str_replace($search, $value, $this->subject);

            // Parse body
            $this->body = str_replace($search, $value, $this->body);

        }

        return $this;
    }
}
