<?php
/**
* @class GitHub
*
* This is a helper to connect with GitHub repositories.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class GitHub {

    /**
    * Get the list of repositories from an user.
    */
    static public function listRepositories($user) {
    	return json_decode(Url::getContents('https://api.github.com/users/'.$user.'/repos'));
    }

    /**
    * Get the information of a single repository.
    */
    static public function infoRepository($user, $repository) {
    	return json_decode(Url::getContents('https://api.github.com/repos/'.$user.'/'.$repository));
    }

    /**
    * Get the information of a single file in a repository.
    */
    static public function infoPath($user, $repository, $path) {
    	return json_decode(Url::getContents('https://api.github.com/repos/'.$user.'/'.$repository.'/contents/'.$path));
    }

}
?>