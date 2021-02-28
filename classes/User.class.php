<?php

class User {

    private $userId;
    private $userName;
    private $firstName;
    private $lastName;
    private $address;
    private $zipCode;
    private $city;
    private $phoneNumber;
    private $mobileNumber;
    private $group;
    private $project;
    private $emailAddress;
    private $IMAddress;
    private $dateRegistered;
    private $password;
    private $rights;
    private $isAdmin;
    private $isProjectLeader;
    private $isGroupLeader;
    private $isTemporary;
    private $isWorking;
    private $isCustomer;
    private $verified;
    private $IPAddress;
    private $userAgent;
    private $userHits;


    function __construct(string $email, string $ip, string $browser, array $row ) {
        $this->userId = $row['UserID'];
        $this->userName = $row['Username'];
        $this->firstName = $row['First name'];
        $this->lastName = $row['Last name'];
        $this->address = $row['Address'];
        $this->zipCode = $row['Zip code'];
        $this->city = $row['City'];
        $this->phoneNumber = $row['Phone number'];
        $this->mobileNumber = $row['Mobile number'];
        $this->group = $row['Group'];
        $this->project = $row['Project'];
        $this->emailAddress = $email;
        $this->IMAddress = $row['IM address'];
        $this->dateRegistered = $row['Date registered'];
        $this->password = $row['Password'];
        $this->rights = $row['Rights'];
        $this->isAdmin = $row['Is admin'];
        $this->isProjectLeader = $row['Is project leader'];
        $this->isGroupLeader = $row['Is group leader'];
        $this->isTemporary = $row['Is temporary'];
        $this->isWorking = $row['Is working'];
        $this->isCustomer = $row['Is customer'];
        $this->verified = $row['Is verified'];
        $this->IPAddress = $ip;
        $this->userAgent = $browser;
        $this->userHits = 0;
    }


    public function verifyUser($request) {
        //$request = Request::createFromGlobals();
        if(($this->IPAddress == $request->server->get('REMOTE_ADDR')) && ($this->UserAgent == $request->server->get('HTTP_USER_AGENT') )){
            $this->usr_hits++;
            return true;
        }
        else
            return false;
    }


    public static function login(PDO $db,  \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session) {
        $email = $request->request->get('Email address');
        try {
            $stmt = $db->prepare("SELECT * FROM Users WHERE `Email address`=:email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if (password_verify($request->request->get('Password'), $row['Password'])) {
                    $session->set('loggedin', true);
                    $ip = $request->server->get('REMOTE_ADDR');
                    $browser = $request->server->get('HTTP_USER_AGENT');
                    $session->set('User', new User($request->request->get('Email address'), $ip, $browser, $row));
                    return true;
                } else {
                    $session->getFlashBag()->add('header', "Wrong username or password");
                    return false;
                }
            }  else {
                $session->getFlashBag()->add('header', "Wrong username or password");
                return false;
            }
        } catch (Exception $e) {
            $session->getFlashBag()->add('header', "Failed to login because of MySQL error");
        }
    }


    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @param mixed $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return mixed
     */
    public function getIMAddress()
    {
        return $this->IMAddress;
    }

    /**
     * @param mixed $IMAddress
     */
    public function setIMAddress($IMAddress)
    {
        $this->IMAddress = $IMAddress;
    }

    /**
     * @return mixed
     */
    public function getDateRegistered()
    {
        return $this->dateRegistered;
    }

    /**
     * @param mixed $dateRegistered
     */
    public function setDateRegistered($dateRegistered)
    {
        $this->dateRegistered = $dateRegistered;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * @param mixed $rights
     */
    public function setRights($rights)
    {
        $this->rights = $rights;
    }

    /**
     * @return mixed
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param mixed $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return mixed
     */
    public function getIsProjectLeader()
    {
        return $this->isProjectLeader;
    }

    /**
     * @param mixed $isProjectLeader
     */
    public function setIsProjectLeader($isProjectLeader)
    {
        $this->isProjectLeader = $isProjectLeader;
    }

    /**
     * @return mixed
     */
    public function getIsGroupLeader()
    {
        return $this->isGroupLeader;
    }

    /**
     * @param mixed $isGroupLeader
     */
    public function setIsGroupLeader($isGroupLeader)
    {
        $this->isGroupLeader = $isGroupLeader;
    }

    /**
     * @return mixed
     */
    public function getIsTemporary()
    {
        return $this->isTemporary;
    }

    /**
     * @param mixed $isTemporary
     */
    public function setIsTemporary($isTemporary)
    {
        $this->isTemporary = $isTemporary;
    }

    /**
     * @return mixed
     */
    public function getIsWorking()
    {
        return $this->isWorking;
    }

    /**
     * @param mixed $isWorking
     */
    public function setIsWorking($isWorking)
    {
        $this->isWorking = $isWorking;
    }

    /**
     * @return mixed
     */
    public function getIsCustomer()
    {
        return $this->isCustomer;
    }

    /**
     * @param mixed $isCustomer
     */
    public function setIsCustomer($isCustomer)
    {
        $this->isCustomer = $isCustomer;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return int
     */
    public function getUserHits(): int
    {
        return $this->userHits;
    }

    /**
     * @param int $userHits
     */
    public function setUserHits(int $userHits)
    {
        $this->userHits = $userHits;
    }
}
?>