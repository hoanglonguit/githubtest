<?php

require_once ('DbConnection.php');
require_once ('./Service/UserGithub.php');
Class UserCrawler
{
	//Database connection
	public $conn;
	private $userGitHubService;
	public function __construct()
	{
		$dbConnection = new DbConnection();
		$this->conn = $dbConnection->conn;
		$this->userGitHubService = new UserGithub();
 

	}

	/**
     * update github information of all records in user table which have created_at is null (uncrawl)
     */  
	public function crawlUserDetail()
	{
		try
		{
			$unCrawlUsers = $this->getAllUncrawlUsers();
		
 			if(count($unCrawlUsers)>0)
		    {
		    	foreach($unCrawlUsers as $user)
		    	{

		    		$loginId = $user['login'];
		    		//get user information from github
					$userDetail = $this->userGitHubService->getUserDetail($loginId);
					if(isset($userDetail->login))
					{
						$this->insertGithubUserToDatabase($userDetail);
					}
		    	}
		    }
		    
		}
		catch(Exception $e)
	    {
	    	echo "error connection Connection failed: " . $e->getMessage();
	    }

	}

	/**
     * get user which have location in parameter and insert github login,id to database
     */  
	public function crawlUserBasicInfo($location='singapore')
	{
		try
		{
			//get total user in database and compare to total user in github
		 	$totalUserInDatabase = $this->getTotalUserInDatabse();
			$totalUserSeachResult = $this->userGitHubService->searchUser($location,'1970-01-01',1,1);

			$githubUserAmount = $totalUserSeachResult->total_count;
			if((int)$totalUserInDatabase >= (int)$githubUserAmount)
			{
				echo 'Finished!';
				return;
			}

			//get created_at of last login user from github and set it started time to crawl , if there is no crawled user  then $startFromDate = '1970-01-01';

		    //get last insert user
		    $lastInsertedUserLogin = $this->getLastInsertedUserLogin();
		    $startFromDate = '1970-01-01';
			$perPage = 100;

		    if($lastInsertedUserLogin)
		    {

				/*$urlUserApi = '/users/'.$lastInsertedUserLogin;
				$firstUser =  json_decode($this->send($urlUserApi, array()));
				*/
				$firstUser = $this->userGitHubService->getUserDetail($lastInsertedUserLogin);
				$createdAt = $firstUser->created_at;
				$timestamp = strtotime($createdAt);
				$startFromDate =  date('Y-m-d', $timestamp);
		    }
			
			//start crawling from created_at

		    //get total result and  get the total page for paging
			$totalUserFromGitHub = $this->getTotalUserFromGithub($location, $startFromDate);
	

			if(!$totalUserFromGitHub)
			{
				echo "stop crawl\n";
				return ;
			}
 

	 		$totalPage = ceil((int)$totalUserFromGitHub/$perPage);
	 	 
	 		for($currentPage=1; $currentPage<=$totalPage; $currentPage++)
	 		{
				$userSeachResult = $this->userGitHubService->searchUser($location,$startFromDate,$currentPage,$perPage);

				if(isset($userSeachResult->message))
				{
					if($userSeachResult->message == "Only the first 1000 search results are available")
					{
						//reach the 1000 limit crawl again from last insert user
						$this->crawlUserBasicInfo($location);
					}
				}

				if(!isset($userSeachResult->total_count))
				{
					return;
				}
				$items = $userSeachResult->items;
				$this->insertUsersToDatabase($items);
				sleep(5);
	 		}


 		}
		catch(PDOException $e)
	    {
	    	echo "error connection Connection failed: " . $e->getMessage();
	    }
	    echo 'finished';
	}
	/**
     * get users amount of specific location which created from  '$startFromDate'
     */  
	public function getTotalUserFromGithub($location, $startFromDate)
	{
		$result = $this->userGitHubService->searchUser($location,$startFromDate,1,1);
		if(!isset($result->total_count))
		{
			return 0;
		}
		return $result->total_count;
	}

	private function insertGithubUserToDatabase($userDetail)
	{
		try
		{
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);				
			$stmt = $this->conn->prepare("UPDATE `user` SET `avatar_url` = :avatar_url, `gravatar_id` = :gravatar_id, `url` = :url, `html_url` = :html_url, `followers_url` = :followers_url, `following_url` = :following_url, `gists_url` = :gists_url, `starred_url` = :starred_url, `subscriptions_url` = :subscriptions_url, `organizations_url` = :organizations_url, `repos_url` = :repos_url, `events_url` = :events_url, `received_events_url` = :received_events_url, `type` = :type, `site_admin` = :site_admin, `name` = :name, `company` = :company, `blog` = :blog, `location` = :location, `email` = :email, `hireable` = :hireable, `bio` = :bio, `public_repos` = :public_repos, `public_gists` = :public_gists, `followers` = :followers, `following` = :following, `created_at` = :created_at, `updated_at` = :updated_at WHERE `user`.`login` = :login");
			$stmt->bindParam(':login', $userDetail->login);
			$stmt->bindParam(':avatar_url', $userDetail->avatar_url);
			$stmt->bindParam(':gravatar_id', $userDetail->gravatar_id);
			$stmt->bindParam(':url', $userDetail->url);
			$stmt->bindParam(':html_url', $userDetail->html_url);
			$stmt->bindParam(':followers_url', $userDetail->followers_url);
			$stmt->bindParam(':following_url', $userDetail->following_url);
			$stmt->bindParam(':gists_url', $userDetail->gists_url);
			$stmt->bindParam(':starred_url', $userDetail->starred_url);
			$stmt->bindParam(':subscriptions_url', $userDetail->subscriptions_url);
			$stmt->bindParam(':organizations_url', $userDetail->organizations_url);
			$stmt->bindParam(':repos_url', $userDetail->repos_url);
			$stmt->bindParam(':events_url', $userDetail->events_url);
			$stmt->bindParam(':received_events_url', $userDetail->received_events_url);
			$stmt->bindParam(':type', $userDetail->type);
			$stmt->bindParam(':site_admin', $userDetail->site_admin);
			$stmt->bindParam(':name', $userDetail->name);
			$stmt->bindParam(':company', $userDetail->company);
			$stmt->bindParam(':blog', $userDetail->blog);
			$stmt->bindParam(':location', $userDetail->location);
			$stmt->bindParam(':email', $userDetail->email);
			$stmt->bindParam(':hireable', $userDetail->hireable);
			$stmt->bindParam(':bio', $userDetail->bio);
			$stmt->bindParam(':public_repos', $userDetail->public_repos);
			$stmt->bindParam(':public_gists', $userDetail->public_gists);
			$stmt->bindParam(':followers', $userDetail->followers);
			$stmt->bindParam(':following', $userDetail->following);
			$stmt->bindParam(':created_at', $userDetail->created_at);
			$stmt->bindParam(':updated_at', $userDetail->updated_at);
			$stmt->execute();
		}
		catch(PDOException $e)
	    {
	    	echo "error connection Connection failed: " . $e->getMessage();
	    }
	}
	private function insertUsersToDatabase($items)
	{
		if(!$items)
		{
			return;
		}

		$loginIdArr = array();
		$userInfo = array();
		foreach($items as $item)
		{
			$userInfo[] = array($item->login,$item->id);
			$loginIdArr[] = $item->login;
		}

	    $insert_values = array();
	    $data  = array();
	    $question_marks = array();

	    foreach($userInfo as $user)
	    {
	    	$data [] = array('login'=>$user[0],'id'=>$user[1]);

	    }
		foreach($data as $d)
		{
		    $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';
		    $insert_values = array_merge($insert_values, array_values($d));
		}
		$sql = "INSERT INTO user (login,id) VALUES " . implode(',', $question_marks);
		$sql .=" ON DUPLICATE KEY UPDATE login = VALUES(login)";
		try {
			$this->conn->beginTransaction();
			$stmt = $this->conn->prepare ($sql);
		    $stmt->execute($insert_values);
		} catch (PDOException $e){
		    echo 'error:'.$e->getMessage();
		}
		$this->conn->commit();
	}

	private function getTotalUserInDatabse()
	{
		$stmt = $this->conn->prepare("SELECT count(*) as total_user FROM user"); 
		$stmt->setFetchMode(PDO::FETCH_ASSOC); 
		$stmt->execute();
		$result = $stmt->fetchAll();
		return $result[0]['total_user'];
	}

	private function getLastInsertedUserLogin()
	{
		 //get last insert user
	    $stmt = $this->conn->prepare("SELECT login FROM user order by id desc, increment_id desc  limit 1"); 
	    $stmt->execute();
	    // set the resulting array to associative
	    $stmt->setFetchMode(PDO::FETCH_ASSOC); 
	    $lastInsertedUser =  $stmt->fetchAll();
	    $lastLogin = '';
	    if(count($lastInsertedUser)>0)
	    {
	    	$lastLogin = $lastInsertedUser[0]['login'];
	    }

	    return $lastLogin;
	}
	
	public function getAllUncrawlUsers()
	{
	    $stmt = $this->conn->prepare("SELECT login FROM user  where created_at is null order by id desc"); 
	    $stmt->setFetchMode(PDO::FETCH_ASSOC); 
	    $stmt->execute();
		$result = $stmt->fetchAll();
		return $result;
	}
	
	function placeholders($text, $count=0, $separator=","){
	    $result = array();
	    if($count > 0){
	        for($x=0; $x<$count; $x++){
	            $result[] = $text;
	        }
	    }

	    return implode($separator, $result);
	}
}