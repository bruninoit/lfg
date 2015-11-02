<?php
/**
*
* @package Limit For Guest
* @copyright (c) 2015 Bruninoit
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace bruninoit\lfg\event;
/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
* Event listener
*/
class lfg implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.viewtopic_modify_post_row'					=> 'viewtopic_lfg',
			);
	}
	/* @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\user */
	protected $user;

	protected $root_path;
	protected $phpEx;
	/** @var \phpbb\auth\auth */
	protected $auth;
    /** @var \phpbb\controller\helper */
    protected $controller_helper;
	protected $request;
protected $config;
	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbb\template			$template	Template object
	*/
	public function __construct(\phpbb\controller\helper $controller_helper, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, $root_path, $phpEx, \phpbb\auth\auth $auth, \phpbb\request\request $request, \phpbb\config\config $config)	{
        $this->controller_helper = $controller_helper;
        $this->template = $template;
		$this->db = $db;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->phpEx   = $phpEx;
		$this->auth = $auth;
       $this->request = $request;
       $this->config = $config;
   
//numero di topic che può vedere un ospite
//number of topics that a guest can view
   $this->max_topics=8;


	}


	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'bruninoit/lfg',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function viewtopic_lfg($event)
	{
    //$topic_data = $event['topic_data'];
    //$topic_id = $topic_data['topic_id'];
    $post_row = $event['post_row'];
    $current_row_number = $event['current_row_number'];
    $message = $post_row['MESSAGE'];
   $att = $post_row['S_HAS_ATTACHMENTS'];
    $user_id = $this->user->data['user_id'];
    $max = $this->max_topics;
    $read = $cookie=$this->request->variable($this->config['cookie_name'] . '_cookie_lfg', '', true, \phpbb\request\request_interface::COOKIE);



   if($user_id==1 and $current_row_number==0)
   {
     if($read>=$max) //topic finiti
     {
       $message = $this->user->lang['MAX_GUEST'];
       $att = false;
     }else{
       $this->user->set_cookie('cookie_lfg', $read+1, strtotime('+1 year'));
     }
    }

$post_row['S_HAS_ATTACHMENTS'] = $att;
$post_row['MESSAGE'] = $message;
$event['post_row'] = $post_row;
 } 

}
