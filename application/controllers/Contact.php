<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends User_Controller {

    ///////////////////////////////////////////////////////////////////////////
    ///  Properties   ////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////

    /**
     * Contact Us Validation
     *
     * @access protected
     */
    protected $contact_validation = array(
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|required|valid_email|lower'
        ),
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'trim|max_length[255]'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last Name',
            'rules' => 'trim|max_length[255]'
        ),
        array(
            'field' => 'message',
            'label' => 'Message',
            'rules' => 'trim'
        ),
    );

    /**
     * Custom Quote Validation
     *
     * @access protected
     */
    protected $quote_validation = array(
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|required|valid_email|lower'
        ),
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'trim|max_length[255]'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last Name',
            'rules' => 'trim|max_length[255]'
        ),
        'company' => array(
            'field' => 'company',
            'label' => 'Company',
            'rules' => 'trim|max_length[255]'
        ),
        array(
            'field' => 'phone',
            'label' => 'Phone',
            'rules' => 'trim|max_length[25]'
        ),
        array(
            'field' => 'contract_count',
            'label' => 'Contracts',
            'rules' => 'trim|required'
        ),
    );


    ///////////////////////////////////////////////////////////////////////////
    ///  Methods   ///////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->lang->load('contact');
    }

    public function contact_us()
    {
	    //log_message('required','::contact_us $_GET: '.print_r($_GET,true));
	    //log_message('required','::contact_us $_REQUEST: '.print_r($_REQUEST,true));
	    //log_message('required','::contact_us $_SERVER: '.print_r($_SERVER,true));

		if (!empty($_SERVER['HTTP_REFERER']) 
			&& strpos($_SERVER['HTTP_REFERER'],'https://www.contracthound.com') === 0
			&& !empty($_GET['email'])) {
			$_POST = $_GET;
			$_SERVER['REQUEST_METHOD'] = 'post';
		}

	    //log_message('required','::contact_us $_POST: '.print_r($_POST,true));
        if (!$this->_isPost()) {
	    	log_message('error','::contact_us not post');
            redirect(ConfigService::getItem('marketing_site_url'));
        }

		$mReturn = $this->form_validation->set_rules($this->contact_validation);
		if ($this->form_validation->run()) {
            $sFirstName = $this->input->post('first_name');
            $sLastName = $this->input->post('last_name');
            $sEmail = $this->input->post('email');
            $sMessage = $this->input->post('message');

            log_message('required',"contact_us / $sFirstName $sLastName : $sEmail / $sMessage");

            $sSubject = $this->lang->line('contact_us_email_subject');
            $sMessageHTML = $this->lang->line('contact_us_email_message_html');
            $sMessageHTML = str_replace(array('%%FIRST_NAME%%', '%%LAST_NAME%%', '%%EMAIL%%', '%%MESSAGE%%')
                , array($sFirstName,$sLastName, $sEmail, $sMessage), $sMessageHTML);

            $sMessageText = $this->lang->line('contact_us_email_message_text');
            $sMessageText = str_replace(array('%%FIRST_NAME%%', '%%LAST_NAME%%', '%%EMAIL%%', '%%MESSAGE%%')
                , array($sFirstName,$sLastName, $sEmail, $sMessage), $sMessageText);

            try {
                $this->load->library('HelperService');

                $bSent = HelperService::sendEmail(
                    'sales@contracthound.com',
                    ConfigService::getItem('system_email'),
                    $sSubject,
                    $sMessageText,
                    $sMessageHTML
                );

                //Redirect to confirmation page
                //redirect(ConfigService::getItem('sales_email') . '/confirmation/');
                $this->load->view('contact/confirmation', $this->aData);
                return;

            } catch (Exception $ex) {
                echo $ex->getMessage();
				log_message('error','::contact_us Exception: '.$ex->getMessage());
            }
        } else {
	        //echo '<pre>'; var_dump($this->form_validation); return true;
            $this->session->current_error($this->form_validation->first_error());
			log_message('error','::contact_us validation failed: '.$this->form_validation->first_error());
        }

		//echo '<pre>'; var_dump(shit); return true;
		redirect(ConfigService::getItem('marketing_site_url'));
        return false;
    }

    public function custom_quote()
    {
		if (!empty($_SERVER['HTTP_REFERER']) 
			&& strpos($_SERVER['HTTP_REFERER'],'https://www.contracthound.com') === 0
			&& !empty($_GET['email'])) {
			$_POST = $_GET;
			$_SERVER['REQUEST_METHOD'] = 'post';
		}

        if (!$this->_isPost()) {
            redirect(ConfigService::getItem('marketing_site_url'));
        }

        $this->form_validation->set_rules($this->quote_validation);
        if ($this->form_validation->run()) {
            $sFirstName = $this->input->post('first_name');
            $sLastName = $this->input->post('last_name');
            $sCompany = $this->input->post('company');
            $sContractCount = $this->input->post('contract_count');
            $sEmail = $this->input->post('email');
            $sPhone = $this->input->post('phone');

            log_message('required',"custom quote / $sFirstName $sLastName : $sEmail : $sPhone / $sCompany / $sContractCount");

            $sSubject = $this->lang->line('custom_quote_email_subject');
            $sMessageHTML = $this->lang->line('custom_quote_email_message_html');
            $sMessageHTML = str_replace(array('%%FIRST_NAME%%', '%%LAST_NAME%%', '%%COMPANY%%', '%%CONTRACT_COUNT%%', '%%EMAIL%%', '%%PHONE%%')
                , array($sFirstName,$sLastName, $sCompany, $sContractCount, $sEmail, $sPhone), $sMessageHTML);

            $sMessageText = $this->lang->line('custom_quote_email_message_text');
            $sMessageText = str_replace(array('%%FIRST_NAME%%', '%%LAST_NAME%%', '%%COMPANY%%', '%%CONTRACT_COUNT%%', '%%EMAIL%%', '%%PHONE%%')
                , array($sFirstName,$sLastName, $sCompany, $sContractCount, $sEmail, $sPhone), $sMessageText);

            try {
                $this->load->library('HelperService');

                $bSent = HelperService::sendEmail(
                    ConfigService::getItem('sales_email'),
                    ConfigService::getItem('system_email'),
                    $sSubject,
                    $sMessageText,
                    $sMessageHTML
                );

                //Redirect to confirmation page
                //redirect(ConfigService::getItem('sales_email') . '/confirmation/');
                $this->load->view('contact/confirmation', $this->aData);
                return;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }

        redirect(ConfigService::getItem('marketing_site_url'));
        return false;

    }
}
