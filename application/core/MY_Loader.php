<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------
// Joseff Betancourt - 11/14/2012 modified code by adding a autoloader and interface loader copied from model loader.

/**
 * Loader Class
 *
 * Loads views and files
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Loader
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class MY_Loader extends CI_Loader {

	// All these are set automatically. Don't mess with them.
	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var int
	 * @access protected
	 */

    protected $_ci_abstracts_paths		= array();
    /**
     * List of paths to load abstracts from
     *
     * @var array
     * @access protected
     */
    protected $_ci_interfaces_paths		= array();
	/**
	 * List of paths to load interfaces from
	 *
	 * @var array
	 * @access protected
	 */

    protected $_ci_abstracts			= array();
    /**
     * List of loaded abstracts
     *
     * @var array
     * @access protected
     */
    protected $_ci_interfaces			= array();
	/**
	 * List of loaded interfaces
	 *
	 * @var array
	 * @access protected
	 */

	/**
	 * Constructor
	 *
	 * Sets the path to the view files and gets the initial output buffering level
	 */

    function __construct()
    {
        parent::__construct();
        $this->_ci_abstracts_paths = array(APPPATH);
        $this->_ci_interfaces_paths = array(APPPATH);
        log_message('debug', "Loader Class Initialized");
    }

	// --------------------------------------------------------------------

	/**
	 * Initialize the Loader
	 *
	 * This method is called once in CI_Controller.
	 *
	 * @param 	array
	 * @return 	object
	 */
	public function initialize()
	{

		$this->_ci_abstracts = array();
        $this->_ci_interfaces = array();
		$this->_ci_autoloader();

		return $this;
	}

	// --------------------------------------------------------------------

    /**
     * Abstracts Loader
     *
     * This function lets users load and instantiate models.
     *
     * 11/14/2012 - Joseff Betancourt - Cloned from Models
     *
     * @param	string	the name of the class
     * @param	string	name for the abstract
     * @param	bool	database connection
     * @return	void
     */
    public function abstracts($abstracts, $name = '', $db_conn = FALSE)
    {
        if (is_array($abstracts))
        {
            foreach ($abstracts as $babe)
            {
                $this->abstracts($babe);
            }
            return;
        }

        if ($abstracts == '')
        {
            return;
        }

        $path = '';

        // Is the abstracts in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($abstracts, '/')) !== FALSE)
        {
            // The path is in front of the last slash
            $path = substr($abstracts, 0, $last_slash + 1);

            // And the model name behind it
            $abstracts = substr($abstracts, $last_slash + 1);
        }

        if ($name == '')
        {
            $name = $abstracts;
        }

        if (in_array($name, $this->_ci_abstracts, TRUE))
        {
            return;
        }

        $CI =& get_instance();
        if (isset($CI->$name))
        {
            show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
        }

        $abstracts = strtolower($abstracts);

        foreach ($this->_ci_abstracts_paths as $mod_path)
        {
            if ( ! file_exists($mod_path.'abstracts/'.$path.$abstracts.'.php'))
            {
                continue;
            }

            if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
            {
                if ($db_conn === TRUE)
                {
                    $db_conn = '';
                }

                $CI->load->database($db_conn, FALSE, TRUE);
            }

            if ( ! class_exists('CI_Abstracts'))
            {
                load_class('Abstracts', 'core');
            }

            require_once($mod_path.'abstracts/'.$path.$abstracts.'.php');

            $abstracts = ucfirst($abstracts);

            $CI->$name = new $abstracts();

            $this->_ci_abstracts[] = $name;
            return;
        }

        // couldn't find the abstracts
        show_error('Unable to locate the abstracts you have specified: '.$abstracts);
    }

    // --------------------------------------------------------------------

    /**
     * Interface Loader
     *
     * This function lets users load and instantiate interfaces.
     *
     * 11/14/2012 - Joseff Betancourt - Cloned from Models
     *
     * @param	string	the name of the class
     * @param	string	name for the interface
     * @param	bool	database connection
     * @return	void
     */
    public function interfaces($interfaces, $name = '', $db_conn = FALSE)
    {
        if (is_array($interfaces))
        {
            foreach ($interfaces as $babe)
            {
                $this->interfaces($babe);
            }
            return;
        }

        if ($interfaces == '')
        {
            return;
        }

        $path = '';

        // Is the abstracts in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($interfaces, '/')) !== FALSE)
        {
            // The path is in front of the last slash
            $path = substr($interfaces, 0, $last_slash + 1);

            // And the model name behind it
            $interfaces = substr($interfaces, $last_slash + 1);
        }

        if ($name == '')
        {
            $name = $interfaces;
        }

        if (in_array($name, $this->_ci_interfaces, TRUE))
        {
            return;
        }

        $CI =& get_instance();
        if (isset($CI->$name))
        {
            show_error('The interface name you are loading is the name of a resource that is already being used: '.$name);
        }

        $interfaces = strtolower($interfaces);

        foreach ($this->_ci_interfaces_paths as $mod_path)
        {
            if ( ! file_exists($mod_path.'interfaces/'.$path.$interfaces.'.php'))
            {
                continue;
            }

            if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
            {
                if ($db_conn === TRUE)
                {
                    $db_conn = '';
                }

                $CI->load->database($db_conn, FALSE, TRUE);
            }

            if ( ! class_exists('CI_Interfaces'))
            {
                load_class('Interfaces', 'core');
            }

            require_once($mod_path.'interfaces/'.$path.$interfaces.'.php');

            $interfaces = ucfirst($interfaces);

            $CI->$name = new $interfaces();

            $this->_ci_interfaces[] = $name;
            return;
        }

        // couldn't find the interfaces
        show_error('Unable to locate the interfaces you have specified: '.$interfaces);
    }

    // --------------------------------------------------------------------


	/**
	 * Autoloader
	 *
	 * The config/autoload.php file contains an array that permits sub-systems,
	 * libraries, and helpers to be loaded automatically.
	 *
	 * @param	array
	 * @return	void
	 */
	private function _ci_autoloader()
	{
        // Abstracts models
        if (isset($autoload['abstracts']))
        {
            $this->model($autoload['abstracts']);
        }

        // Interfaces models
        if (isset($autoload['interfaces']))
        {
            $this->model($autoload['interfaces']);
        }

	}

	// --------------------------------------------------------------------

}

/* End of file Loader.php */
/* Location: ./system/core/Loader.php */