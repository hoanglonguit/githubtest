<?php
require_once ('ServiceAbstract.php');
Class UserGithub extends ServiceAbstract
{
	public function getUserDetail($login)
	{
		$urlUserApi = '/users/'.$login."?access_token=".Config::$github_personal_token; 	 
		$userDetail =  json_decode($this->send($urlUserApi, array()));
		return $userDetail;
	}
	public function searchUser($location,$startFromDate,$currentPage,$perPage)
	{
		$urlSearchApi = '/search/users?q=location:'.$location.'+created:>='.$startFromDate.'&sort=joined&order=asc'.'&page='.$currentPage.'&per_page='.$perPage;
		$result =  json_decode($this->send($urlSearchApi, array()));
		return $result;
	}
}