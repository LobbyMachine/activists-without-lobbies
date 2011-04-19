<?php

// **********************************************************************
// TheyWorkForYou.com API PHP interface
// Version 1.7
// Author: Ruben Arakelyan <ruben@rubenarakelyan.com>
//
// Copyright (C) 1008-2010 Ruben Arakelyan.
// This file is licensed under the licence available at
// http://creativecommons.org/licenses/by-sa/3.0/
//
// For more information, see http://tools.rubenarakelyan.com/twfyapi/
//
// Inspiration: WebService::TWFY::API by Spiros Denaxas
// Available at http://search.cpan.org/~sden/WebService-TWFY-API-0.03/
// **********************************************************************

class TWFYAPI
{

    // API key
    private $key;

    // cURL handle
    private $ch;

    // Default constructor
    public function __construct($key)
    {
        // Check and set API key
        if (!$key)
        {
            die('ERROR: No API key provided.');
        }
        if (!preg_match('/^[A-Za-z0-9]+$/', $key))
        {
            die('ERROR: Invalid API key provided.');
        }
        $this->key = $key;

        // Create a new instance of cURL
        $this->ch = curl_init();

        // Set the user agent
        // It does not provide TheyWorkForYou.com with any personal information
        // but helps them track usage of this PHP class.
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'TheyWorkForYou.com API PHP interface (+http://tools.rubenarakelyan.com/twfyapi/)');

        // Return the result
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    // Default destructor
    public function __destruct()
    {
        // Delete the instance of cURL
        curl_close($this->ch);
    }

    // Send an API query
    public function query($func, $args = array())
    {
        // Exit if any arguments are not defined
        if (!isset($func) || $func == '' || !isset($args) || $args == '' || !is_array($args))
        {
            die('ERROR: Function name or arguments not provided.');
        }

        // Construct the query
        $query = new TWFYAPI_Request($func, $args, $this->key);

        // Execute the query
        if (is_object($query))
        {
            return $this->_execute_query($query);
        }
        else
        {
            die('ERROR: Could not assemble request using TWFYAPI_Request.');
        }
    }

    // Execute an API query
    private function _execute_query($query)
    {
        // Make the final URL
        $URL = $query->encode_arguments();

        // Set the URL
        curl_setopt($this->ch, CURLOPT_URL, $URL);

        // Get the result
        $result = curl_exec($this->ch);

        // Find out if all is OK
        if (!$result)
        {
            die('ERROR: cURL error occurred: ' . curl_error($this->ch));
        }
        else
        {
            return $result;
        }
    }

}


class TWFYAPI_Request
{

    // API URL
    private $URL = 'http://www.theyworkforyou.com/api/';

    // Chosen function, arguments and API key
    private $func;
    private $args;

    // Default constructor
    public function __construct($func, $args, $key)
    {
        // Set function, arguments and API key
        $this->func = $func;
        $this->args = $args;
        $this->key = $key;

        // Get and set the URL
        $this->URL = $this->_get_uri_for_function($this->func);

        // Check to see if valid URL has been set
        if (!isset($this->URL) || $this->URL == '')
        {
            die('ERROR: Invalid function: ' . $this->func . '. Please look at the documentation for supported functions.');
        }
    }

    // Encode function arguments into a URL query string
    public function encode_arguments()
    {
        // Validate the output argument if it exists
        if (array_key_exists('output', $this->args))
        {
            if (!$this->_validate_output_argument($this->args['output']))
            {
                die('ERROR: Invalid output type: ' . $this->args['output'] . '. Please look at the documentation for supported output types.');
            }
        }

        // Make sure all mandatory arguments for a particular function are present
        if (!$this->_validate_arguments($this->func, $this->args))
        {
            die('ERROR: All mandatory arguments for ' . $this->func . ' not provided.');
        }

        // Assemble the URL
        $full_url = $this->URL . '?key=' . $this->key . '&';
        foreach ($this->args as $name => $value)
        {
            $full_url .= $name . '=' . urlencode($value) . '&';
        }
        $full_url = substr($full_url, 0, -1);

        return $full_url;        
    }

    // Get the URL for a particular function
    private function _get_uri_for_function($func)
    {
        // Exit if any arguments are not defined
        if (!isset($func) || $func == '')
        {
            return '';
        }

        // Define valid functions
        $valid_functions = array(
          'convertURL'        => 'Converts a parliament.uk URL into a TheyWorkForYou one, if possible',
          'getConstituency'   => 'Searches for a constituency',
          'getConstituencies' => 'Returns list of constituencies',
          'getPerson'         => 'Returns main details for a person',
          'getMP'             => 'Returns main details for an MP',
          'getMPInfo'         => 'Returns extra information for a person',
          'getMPsInfo'        => 'Returns extra information for one or more people',
          'getMPs'            => 'Returns list of MPs',
          'getLord'           => 'Returns details for a Lord',
          'getLords'          => 'Returns list of Lords',
          'getMLA'            => 'Returns details for an MLA',
          'getMLAs'           => 'Returns list of MLAs',
          'getMSP'            => 'Returns details for an MSP',
          'getMSPs'           => 'Returns list of MSPs',
          'getGeometry'       => 'Returns centre, bounding box of constituencies',
          'getCommittee'      => 'Returns members of Select Committee',
          'getDebates'        => 'Returns Debates (either Commons, Westminster Hall, or Lords)',
          'getWrans'          => 'Returns Written Answers',
          'getWMS'            => 'Returns Written Ministerial Statements',
          'getHansard'        => 'Returns any of the above',
          'getComments'       => 'Returns comments',
        );

        // If the function exists, return its URL
        if (array_key_exists($func, $valid_functions))
        {
            return $this->URL . $func;
        }
        else
        {
            return '';
        }
    }

    // Validate the "output" argument
    private function _validate_output_argument($output)
    {
        // Exit if any arguments are not defined
        if (!isset($output) || $output == '')
        {
            return false;
        }

        // Define valid output types
        $valid_params = array(
          'xml'  => 'XML output',
          'php'  => 'Serialized PHP',
          'js'   => 'a JavaScript object', 
          'rabx' => 'RPC over Anything But XML',
        );

        // Check to see if the output type provided is valid
        if (array_key_exists($output, $valid_params))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    // Validate arguments
    private function _validate_arguments($func, $args)
    {
        // Define manadatory arguments
        $functions_params = array(
          'convertURL'        => array( 'url' ),
          'getConstituency'   => array( 'postcode' ),
          'getConstituencies' => array( ),
          'getPerson'         => array( 'id' ),
          'getMP'             => array( ),
          'getMPInfo'         => array( 'id' ),
          'getMPs'            => array( ),
          'getLord'           => array( 'id' ),
          'getLords'          => array( ),
          'getMLA'            => array( ),
          'getMLAs'           => array( ),
          'getMSP'            => array( ),
          'getMSPs'           => array( ),
          'getGeometry'       => array( ),
          'getCommittee'      => array( 'name' ),
          'getDebates'        => array( 'type' ),
          'getWrans'          => array( ),
          'getWMS'            => array( ),
          'getHansard'        => array( ),
          'getComments'       => array( ),
        );

        // Check to see if all mandatory arguments are present
        $required_params = $functions_params[$func];
        foreach ($required_params as $param)
        {
            if (!isset($args[$param]))
            {
                return false;
            }
        }

        return true;
    }


}
