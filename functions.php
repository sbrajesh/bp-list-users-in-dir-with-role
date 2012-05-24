<?php

//get all roles available in the current install
//return an arry of roles=>rolename as key value pairs for all the available roles

function bpdev_get_available_roles() {
	global $wp_roles;
        if(empty($wp_roles))
            $wp_roles=new WP_Roles();

	$all_roles= $wp_roles->get_names();//all roles as role=>role_name
        
        return $all_roles;
        
  
}
//returns array of role=>count of users(It will only return the roles for which the count is greater than 0
function bpdev_get_users_count_by_role(){
    $users=count_users( 'memory' );
    $available_roles=$users['avail_roles'];//will have an array where key is the role and value is the user count, array role=>count
    return $available_roles;
}
/**
 * return an arry of user ids based on roles
 * @param type $role
 * @return arry of user ids 
 */
function bpdev_get_users_by_role($role){
       
    $user_ids=array();
    $users= get_users( array( 'role' => $role ) );
   if(!empty($users)){
       foreach((array)$users as $user)
           $user_ids[]=$user->ID;
       
   }
   return $user_ids;
}


//hook to the query string
add_action('bp_ajax_querystring','bpdev_theme_exclude_users',20,2);
function bpdev_theme_exclude_users($qs=false,$object=false){
    if($object!='members')//hide for members only
        return $qs;
    
    $args=wp_parse_args($qs);
    extract($args);
    if(empty($scope)||$scope=='all'||$scope=='personal')
        return $qs;
    //check if we are searching for friends list etc?, do not exclude in this case
    if(!empty($args['user_id'])||!empty($args['search_terms']))
        return $qs;
    
    
    //list of users to include
      $included_users=bpdev_get_users_by_role($scope);
      //join it
      if(!empty($included_users))
          $included_users=join(',',$included_users);
      else 
          $included_users=0;//hacking around, can you guess why I am doing this :D ok, I will tell ya, Just to avoid listing any thing when a role has no users in it
          
       
    
    if(!empty($args['include']))
        $args['include']=$args['include'].','.$included_users;
    else 
        $args['include']=$included_users;
      
    $qs=build_query($args);
   
   
   return $qs;
    
}

?>