<?php
class Users_model extends MY_Model {
	public function getUserIdByName($userName){
		$user=$this->db->get_where("users",array("username"=>$userName),1)->row();
		if($user){
			return $user->id;
		}
		
	}
	public function logIn($data){
		$result=$this->db->select("*")
				->from("users")
				->where("username",$data["username"])
				->get()
				->row_array();
		if($result){
			if($result['hasActivated']==1){
				if(password_verify($data['password'],$result['password'])){
					$this->session->set_userdata("userId",$result['id']);
					return false;
				} else {
					return "The name or password did not match";
				}
			} else {
				return "The user exist but is not yet activated.";
			}
		} else {
			return "The name or password did not match";
		}
	}
	public function register($data){
		$user	=	$this->db->select("*")
					->from("users")
					->where("username",$data["username"])
					->or_where("email",$data['mail'])
					->get()
					->row_array();
		if($user){
			if($user['email']==$data['mail']){
				return "This email is already in use, please choose a different one.";
			}
			if($user['username']==$data['username']){
				return "This username is already in use, please choose a different one.";
			}
		}
		$id=parent::generateId("users");
		$this->load->helper("string");
		$randomActivationString=random_string("alpha", 32);
		$insertData=array("id"=>$id,
			"username"=>$data['username'],
			"password"=>password_hash($data['password'],PASSWORD_DEFAULT),
			"email"=>$data['mail'],
			"activationCode"=>$randomActivationString,
			"hasActivated"=>0
		);
		$this->load->library('email');
		$this->db->insert("users",$insertData);
		$this->email->from('no_reply@mud.com', 'My MUD');
		$this->email->to($data['mail']);
		$this->email->subject('Activate account');
		$this->email->message('<!doctype html><html></h
		tml><body><h1>Uw account at My MUD is ready to be activated</h1><p>You can activate it <a href="'.base_url("index.php/activation/".$randomActivationString).'">here</a></p></body></html>');

		$this->email->send();
	}
	//activates the user so he can play. As that is what everyone wants to do these days.
	public function activate($activateString){
		//get the user by its activation code
		$user	=	$this->db->select("id")
					->from("users")
					->where("activationCode",$activateString)
					->get()
					->row_array();
		//check if there is one, return the error string if it was not found
		if(!$user){
			return "The given activation string doesn't exist. It may be that the user is already acctivated.";
		}
		//user was found, so lets be nice and activate him/her :)
		$this->db->where($user);
		$this->db->update("users",array("activationCode"=>"","hasActivated"=>1));
	}
	public function getUserData($userId){
		$data=array();
		$data	=	$this->db->select("username")
					->from("users")
					->where("id",$userId)
					->get()
					->row_array();
		return $data;
	}
	public function getGravFromEmail($email){
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?&d=http://claimygift.com/uploads/avatars/noavatar.png";
		return $url;
	}
	public function replaceEmailWithGravInList($userList){
		foreach($userList as $key=>$value){
			$userList[$key]["avatar"]	=	$this->getGravFromEmail($value["email"]);
			unset($userList[$key]["email"]);
		}
		return $userList;
	}

}
