<?php
/**
 * WooCommerce countries
 * 
 * The WooCommerce countries class stores country/state data.
 *
 * @class 		WC_Countries
 * @package		WooCommerce
 * @category	Class
 * @author		WooThemes
 */
class WC_Countries {
	
	var $countries;
	var $states;
	var $locale;
	var $address_formats;
	
	/**
	 * Constructor
	 */
	function __construct() {
	
		$this->countries = array(
			'AF' => __('Afghanistan', 'woocommerce'),
			'AX' => __('Aland Islands', 'woocommerce'),
			'AL' => __('Albania', 'woocommerce'),
			'DZ' => __('Algeria', 'woocommerce'),
			'AS' => __('American Samoa', 'woocommerce'),
			'AD' => __('Andorra', 'woocommerce'),
			'AO' => __('Angola', 'woocommerce'),
			'AI' => __('Anguilla', 'woocommerce'),
			'AQ' => __('Antarctica', 'woocommerce'),
			'AG' => __('Antigua and Barbuda', 'woocommerce'),
			'AR' => __('Argentina', 'woocommerce'),
			'AM' => __('Armenia', 'woocommerce'),
			'AW' => __('Aruba', 'woocommerce'),
			'AU' => __('Australia', 'woocommerce'),
			'AT' => __('Austria', 'woocommerce'),
			'AZ' => __('Azerbaijan', 'woocommerce'),
			'BS' => __('Bahamas', 'woocommerce'),
			'BH' => __('Bahrain', 'woocommerce'),
			'BD' => __('Bangladesh', 'woocommerce'),
			'BB' => __('Barbados', 'woocommerce'),
			'BY' => __('Belarus', 'woocommerce'),
			'BE' => __('Belgium', 'woocommerce'),
			'BZ' => __('Belize', 'woocommerce'),
			'BJ' => __('Benin', 'woocommerce'),
			'BM' => __('Bermuda', 'woocommerce'),
			'BT' => __('Bhutan', 'woocommerce'),
			'BO' => __('Bolivia', 'woocommerce'),
			'BA' => __('Bosnia and Herzegovina', 'woocommerce'),
			'BW' => __('Botswana', 'woocommerce'),
			'BR' => __('Brazil', 'woocommerce'),
			'IO' => __('British Indian Ocean Territory', 'woocommerce'),
			'VG' => __('British Virgin Islands', 'woocommerce'),
			'BN' => __('Brunei', 'woocommerce'),
			'BG' => __('Bulgaria', 'woocommerce'),
			'BF' => __('Burkina Faso', 'woocommerce'),
			'BI' => __('Burundi', 'woocommerce'),
			'KH' => __('Cambodia', 'woocommerce'),
			'CM' => __('Cameroon', 'woocommerce'),
			'CA' => __('Canada', 'woocommerce'),
			'CV' => __('Cape Verde', 'woocommerce'),
			'KY' => __('Cayman Islands', 'woocommerce'),
			'CF' => __('Central African Republic', 'woocommerce'),
			'TD' => __('Chad', 'woocommerce'),
			'CL' => __('Chile', 'woocommerce'),
			'CN' => __('China', 'woocommerce'),
			'CX' => __('Christmas Island', 'woocommerce'),
			'CC' => __('Cocos (Keeling) Islands', 'woocommerce'),
			'CO' => __('Colombia', 'woocommerce'),
			'KM' => __('Comoros', 'woocommerce'),
			'CG' => __('Congo (Brazzaville)', 'woocommerce'),
			'CD' => __('Congo (Kinshasa)', 'woocommerce'),
			'CK' => __('Cook Islands', 'woocommerce'),
			'CR' => __('Costa Rica', 'woocommerce'),
			'HR' => __('Croatia', 'woocommerce'),
			'CU' => __('Cuba', 'woocommerce'),
			'CY' => __('Cyprus', 'woocommerce'),
			'CZ' => __('Czech Republic', 'woocommerce'),
			'DK' => __('Denmark', 'woocommerce'),
			'DJ' => __('Djibouti', 'woocommerce'),
			'DM' => __('Dominica', 'woocommerce'),
			'DO' => __('Dominican Republic', 'woocommerce'),
			'EC' => __('Ecuador', 'woocommerce'),
			'EG' => __('Egypt', 'woocommerce'),
			'SV' => __('El Salvador', 'woocommerce'),
			'GQ' => __('Equatorial Guinea', 'woocommerce'),
			'ER' => __('Eritrea', 'woocommerce'),
			'EE' => __('Estonia', 'woocommerce'),
			'ET' => __('Ethiopia', 'woocommerce'),
			'FK' => __('Falkland Islands', 'woocommerce'),
			'FO' => __('Faroe Islands', 'woocommerce'),
			'FJ' => __('Fiji', 'woocommerce'),
			'FI' => __('Finland', 'woocommerce'),
			'FR' => __('France', 'woocommerce'),
			'GF' => __('French Guiana', 'woocommerce'),
			'PF' => __('French Polynesia', 'woocommerce'),
			'TF' => __('French Southern Territories', 'woocommerce'),
			'GA' => __('Gabon', 'woocommerce'),
			'GM' => __('Gambia', 'woocommerce'),
			'GE' => __('Georgia', 'woocommerce'),
			'DE' => __('Germany', 'woocommerce'),
			'GH' => __('Ghana', 'woocommerce'),
			'GI' => __('Gibraltar', 'woocommerce'),
			'GR' => __('Greece', 'woocommerce'),
			'GL' => __('Greenland', 'woocommerce'),
			'GD' => __('Grenada', 'woocommerce'),
			'GP' => __('Guadeloupe', 'woocommerce'),
			'GU' => __('Guam', 'woocommerce'),
			'GT' => __('Guatemala', 'woocommerce'),
			'GG' => __('Guernsey', 'woocommerce'),
			'GN' => __('Guinea', 'woocommerce'),
			'GW' => __('Guinea-Bissau', 'woocommerce'),
			'GY' => __('Guyana', 'woocommerce'),
			'HT' => __('Haiti', 'woocommerce'),
			'HN' => __('Honduras', 'woocommerce'),
			'HK' => __('Hong Kong', 'woocommerce'),
			'HU' => __('Hungary', 'woocommerce'),
			'IS' => __('Iceland', 'woocommerce'),
			'IN' => __('India', 'woocommerce'),
			'ID' => __('Indonesia', 'woocommerce'),
			'IR' => __('Iran', 'woocommerce'),
			'IQ' => __('Iraq', 'woocommerce'),
			'IE' => __('Ireland', 'woocommerce'),
			'IM' => __('Isle of Man', 'woocommerce'),
			'IL' => __('Israel', 'woocommerce'),
			'IT' => __('Italy', 'woocommerce'),
			'CI' => __('Ivory Coast', 'woocommerce'),
			'JM' => __('Jamaica', 'woocommerce'),
			'JP' => __('Japan', 'woocommerce'),
			'JE' => __('Jersey', 'woocommerce'),
			'JO' => __('Jordan', 'woocommerce'),
			'KZ' => __('Kazakhstan', 'woocommerce'),
			'KE' => __('Kenya', 'woocommerce'),
			'KI' => __('Kiribati', 'woocommerce'),
			'KW' => __('Kuwait', 'woocommerce'),
			'KG' => __('Kyrgyzstan', 'woocommerce'),
			'LA' => __('Laos', 'woocommerce'),
			'LV' => __('Latvia', 'woocommerce'),
			'LB' => __('Lebanon', 'woocommerce'),
			'LS' => __('Lesotho', 'woocommerce'),
			'LR' => __('Liberia', 'woocommerce'),
			'LY' => __('Libya', 'woocommerce'),
			'LI' => __('Liechtenstein', 'woocommerce'),
			'LT' => __('Lithuania', 'woocommerce'),
			'LU' => __('Luxembourg', 'woocommerce'),
			'MO' => __('Macao S.A.R., China', 'woocommerce'),
			'MK' => __('Macedonia', 'woocommerce'),
			'MG' => __('Madagascar', 'woocommerce'),
			'MW' => __('Malawi', 'woocommerce'),
			'MY' => __('Malaysia', 'woocommerce'),
			'MV' => __('Maldives', 'woocommerce'),
			'ML' => __('Mali', 'woocommerce'),
			'MT' => __('Malta', 'woocommerce'),
			'MH' => __('Marshall Islands', 'woocommerce'),
			'MQ' => __('Martinique', 'woocommerce'),
			'MR' => __('Mauritania', 'woocommerce'),
			'MU' => __('Mauritius', 'woocommerce'),
			'YT' => __('Mayotte', 'woocommerce'),
			'MX' => __('Mexico', 'woocommerce'),
			'FM' => __('Micronesia', 'woocommerce'),
			'MD' => __('Moldova', 'woocommerce'),
			'MC' => __('Monaco', 'woocommerce'),
			'MN' => __('Mongolia', 'woocommerce'),
			'ME' => __('Montenegro', 'woocommerce'),
			'MS' => __('Montserrat', 'woocommerce'),
			'MA' => __('Morocco', 'woocommerce'),
			'MZ' => __('Mozambique', 'woocommerce'),
			'MM' => __('Myanmar', 'woocommerce'),
			'NA' => __('Namibia', 'woocommerce'),
			'NR' => __('Nauru', 'woocommerce'),
			'NP' => __('Nepal', 'woocommerce'),
			'NL' => __('Netherlands', 'woocommerce'),
			'AN' => __('Netherlands Antilles', 'woocommerce'),
			'NC' => __('New Caledonia', 'woocommerce'),
			'NZ' => __('New Zealand', 'woocommerce'),
			'NI' => __('Nicaragua', 'woocommerce'),
			'NE' => __('Niger', 'woocommerce'),
			'NG' => __('Nigeria', 'woocommerce'),
			'NU' => __('Niue', 'woocommerce'),
			'NF' => __('Norfolk Island', 'woocommerce'),
			'KP' => __('North Korea', 'woocommerce'),
			'MP' => __('Northern Mariana Islands', 'woocommerce'),
			'NO' => __('Norway', 'woocommerce'),
			'OM' => __('Oman', 'woocommerce'),
			'PK' => __('Pakistan', 'woocommerce'),
			'PW' => __('Palau', 'woocommerce'),
			'PS' => __('Palestinian Territory', 'woocommerce'),
			'PA' => __('Panama', 'woocommerce'),
			'PG' => __('Papua New Guinea', 'woocommerce'),
			'PY' => __('Paraguay', 'woocommerce'),
			'PE' => __('Peru', 'woocommerce'),
			'PH' => __('Philippines', 'woocommerce'),
			'PN' => __('Pitcairn', 'woocommerce'),
			'PL' => __('Poland', 'woocommerce'),
			'PT' => __('Portugal', 'woocommerce'),
			'PR' => __('Puerto Rico', 'woocommerce'),
			'QA' => __('Qatar', 'woocommerce'),
			'RE' => __('Reunion', 'woocommerce'),
			'RO' => __('Romania', 'woocommerce'),
			'RU' => __('Russia', 'woocommerce'),
			'RW' => __('Rwanda', 'woocommerce'),
			'BL' => __('Saint Barthélemy', 'woocommerce'),
			'SH' => __('Saint Helena', 'woocommerce'),
			'KN' => __('Saint Kitts and Nevis', 'woocommerce'),
			'LC' => __('Saint Lucia', 'woocommerce'),
			'MF' => __('Saint Martin (French part)', 'woocommerce'),
			'PM' => __('Saint Pierre and Miquelon', 'woocommerce'),
			'VC' => __('Saint Vincent and the Grenadines', 'woocommerce'),
			'WS' => __('Samoa', 'woocommerce'),
			'SM' => __('San Marino', 'woocommerce'),
			'ST' => __('Sao Tome and Principe', 'woocommerce'),
			'SA' => __('Saudi Arabia', 'woocommerce'),
			'SN' => __('Senegal', 'woocommerce'),
			'RS' => __('Serbia', 'woocommerce'),
			'SC' => __('Seychelles', 'woocommerce'),
			'SL' => __('Sierra Leone', 'woocommerce'),
			'SG' => __('Singapore', 'woocommerce'),
			'SK' => __('Slovakia', 'woocommerce'),
			'SI' => __('Slovenia', 'woocommerce'),
			'SB' => __('Solomon Islands', 'woocommerce'),
			'SO' => __('Somalia', 'woocommerce'),
			'ZA' => __('South Africa', 'woocommerce'),
			'GS' => __('South Georgia/Sandwich Islands', 'woocommerce'),
			'KR' => __('South Korea', 'woocommerce'),
			'ES' => __('Spain', 'woocommerce'),
			'LK' => __('Sri Lanka', 'woocommerce'),
			'SD' => __('Sudan', 'woocommerce'),
			'SR' => __('Suriname', 'woocommerce'),
			'SJ' => __('Svalbard and Jan Mayen', 'woocommerce'),
			'SZ' => __('Swaziland', 'woocommerce'),
			'SE' => __('Sweden', 'woocommerce'),
			'CH' => __('Switzerland', 'woocommerce'),
			'SY' => __('Syria', 'woocommerce'),
			'TW' => __('Taiwan', 'woocommerce'),
			'TJ' => __('Tajikistan', 'woocommerce'),
			'TZ' => __('Tanzania', 'woocommerce'),
			'TH' => __('Thailand', 'woocommerce'),
			'TL' => __('Timor-Leste', 'woocommerce'),
			'TG' => __('Togo', 'woocommerce'),
			'TK' => __('Tokelau', 'woocommerce'),
			'TO' => __('Tonga', 'woocommerce'),
			'TT' => __('Trinidad and Tobago', 'woocommerce'),
			'TN' => __('Tunisia', 'woocommerce'),
			'TR' => __('Turkey', 'woocommerce'),
			'TM' => __('Turkmenistan', 'woocommerce'),
			'TC' => __('Turks and Caicos Islands', 'woocommerce'),
			'TV' => __('Tuvalu', 'woocommerce'),
			'VI' => __('U.S. Virgin Islands', 'woocommerce'),
			'USAF' => __('US Armed Forces', 'woocommerce'),
			'UM' => __('US Minor Outlying Islands', 'woocommerce'),
			'UG' => __('Uganda', 'woocommerce'),
			'UA' => __('Ukraine', 'woocommerce'),
			'AE' => __('United Arab Emirates', 'woocommerce'),
			'GB' => __('United Kingdom', 'woocommerce'),
			'US' => __('United States', 'woocommerce'),
			'UY' => __('Uruguay', 'woocommerce'),
			'UZ' => __('Uzbekistan', 'woocommerce'),
			'VU' => __('Vanuatu', 'woocommerce'),
			'VA' => __('Vatican', 'woocommerce'),
			'VE' => __('Venezuela', 'woocommerce'),
			'VN' => __('Vietnam', 'woocommerce'),
			'WF' => __('Wallis and Futuna', 'woocommerce'),
			'EH' => __('Western Sahara', 'woocommerce'),
			'YE' => __('Yemen', 'woocommerce'),
			'ZM' => __('Zambia', 'woocommerce'),
			'ZW' => __('Zimbabwe', 'woocommerce')
		);
					
		$this->states = array(
			'AU' => array(
				'ACT' => __('Australian Capital Territory', 'woocommerce') ,
				'NSW' => __('New South Wales', 'woocommerce') ,
				'NT' => __('Northern Territory', 'woocommerce') ,
				'QLD' => __('Queensland', 'woocommerce') ,
				'SA' => __('South Australia', 'woocommerce') ,
				'TAS' => __('Tasmania', 'woocommerce') ,
				'VIC' => __('Victoria', 'woocommerce') ,
				'WA' => __('Western Australia', 'woocommerce') 
			),
			'AT' => array(),
			'BR' => array(
			    'AM' => __('Amazonas', 'woocommerce'),
			    'AC' => __('Acre', 'woocommerce'),
			    'AL' => __('Alagoas', 'woocommerce'),
			    'AP' => __('Amap&aacute;', 'woocommerce'),
			    'CE' => __('Cear&aacute;', 'woocommerce'),
			    'DF' => __('Distrito federal', 'woocommerce'),
			    'ES' => __('Espirito santo', 'woocommerce'),
			    'MA' => __('Maranh&atilde;o', 'woocommerce'),
			    'PR' => __('Paran&aacute;', 'woocommerce'),
			    'PE' => __('Pernambuco', 'woocommerce'),
			    'PI' => __('Piau&iacute;', 'woocommerce'),
			    'RN' => __('Rio grande do norte', 'woocommerce'),
			    'RS' => __('Rio grande do sul', 'woocommerce'),
			    'RO' => __('Rond&ocirc;nia', 'woocommerce'),
			    'RR' => __('Roraima', 'woocommerce'),
			    'SC' => __('Santa catarina', 'woocommerce'),
			    'SE' => __('Sergipe', 'woocommerce'),
			    'TO' => __('Tocantins', 'woocommerce'),
			    'PA' => __('Par&aacute;', 'woocommerce'),
			    'BH' => __('Bahia', 'woocommerce'),
			    'GO' => __('Goi&aacute;s', 'woocommerce'),
			    'MT' => __('Mato grosso', 'woocommerce'),
			    'MS' => __('Mato grosso do sul', 'woocommerce'),
			    'RJ' => __('Rio de janeiro', 'woocommerce'),
			    'SP' => __('S&atilde;o paulo', 'woocommerce'),
			    'RS' => __('Rio grande do sul', 'woocommerce'),
			    'MG' => __('Minas gerais', 'woocommerce'),
			    'PB' => __('Paraiba', 'woocommerce'),
			),
			'CA' => array(
				'AB' => __('Alberta', 'woocommerce') ,
				'BC' => __('British Columbia', 'woocommerce') ,
				'MB' => __('Manitoba', 'woocommerce') ,
				'NB' => __('New Brunswick', 'woocommerce') ,
				'NF' => __('Newfoundland', 'woocommerce') ,
				'NT' => __('Northwest Territories', 'woocommerce') ,
				'NS' => __('Nova Scotia', 'woocommerce') ,
				'NU' => __('Nunavut', 'woocommerce') ,
				'ON' => __('Ontario', 'woocommerce') ,
				'PE' => __('Prince Edward Island', 'woocommerce') ,
				'PQ' => __('Quebec', 'woocommerce') ,
				'SK' => __('Saskatchewan', 'woocommerce') ,
				'YT' => __('Yukon Territory', 'woocommerce') 
			),
			'CZ' => array(),
			'DE' => array(),
			'DK' => array(),
			'FI' => array(),
			'FR' => array(),
			'HK' => array(
				'HONG KONG' => __('Hong Kong Island', 'woocommerce'),
				'KOWLOONG' => __('Kowloong', 'woocommerce'),
				'NEW TERRITORIES' => __('New Territories', 'woocommerce')
			),
			'HU' => array(),
			'IS' => array(),
			'IL' => array(),
			'NL' => array(),
			'NZ' => array(),
			'NO' => array(),
			'PL' => array(),
			'SG' => array(),
			'SK' => array(),
			'SI' => array(),
			'LK' => array(),
			'SE' => array(),
			'US' => array(
				'AL' => __('Alabama', 'woocommerce') ,
				'AK' => __('Alaska', 'woocommerce') ,
				'AZ' => __('Arizona', 'woocommerce') ,
				'AR' => __('Arkansas', 'woocommerce') ,
				'CA' => __('California', 'woocommerce') ,
				'CO' => __('Colorado', 'woocommerce') ,
				'CT' => __('Connecticut', 'woocommerce') ,
				'DE' => __('Delaware', 'woocommerce') ,
				'DC' => __('District Of Columbia', 'woocommerce') ,
				'FL' => __('Florida', 'woocommerce') ,
				'GA' => __('Georgia', 'woocommerce') ,
				'HI' => __('Hawaii', 'woocommerce') ,
				'ID' => __('Idaho', 'woocommerce') ,
				'IL' => __('Illinois', 'woocommerce') ,
				'IN' => __('Indiana', 'woocommerce') ,
				'IA' => __('Iowa', 'woocommerce') ,
				'KS' => __('Kansas', 'woocommerce') ,
				'KY' => __('Kentucky', 'woocommerce') ,
				'LA' => __('Louisiana', 'woocommerce') ,
				'ME' => __('Maine', 'woocommerce') ,
				'MD' => __('Maryland', 'woocommerce') ,
				'MA' => __('Massachusetts', 'woocommerce') ,
				'MI' => __('Michigan', 'woocommerce') ,
				'MN' => __('Minnesota', 'woocommerce') ,
				'MS' => __('Mississippi', 'woocommerce') ,
				'MO' => __('Missouri', 'woocommerce') ,
				'MT' => __('Montana', 'woocommerce') ,
				'NE' => __('Nebraska', 'woocommerce') ,
				'NV' => __('Nevada', 'woocommerce') ,
				'NH' => __('New Hampshire', 'woocommerce') ,
				'NJ' => __('New Jersey', 'woocommerce') ,
				'NM' => __('New Mexico', 'woocommerce') ,
				'NY' => __('New York', 'woocommerce') ,
				'NC' => __('North Carolina', 'woocommerce') ,
				'ND' => __('North Dakota', 'woocommerce') ,
				'OH' => __('Ohio', 'woocommerce') ,
				'OK' => __('Oklahoma', 'woocommerce') ,
				'OR' => __('Oregon', 'woocommerce') ,
				'PA' => __('Pennsylvania', 'woocommerce') ,
				'RI' => __('Rhode Island', 'woocommerce') ,
				'SC' => __('South Carolina', 'woocommerce') ,
				'SD' => __('South Dakota', 'woocommerce') ,
				'TN' => __('Tennessee', 'woocommerce') ,
				'TX' => __('Texas', 'woocommerce') ,
				'UT' => __('Utah', 'woocommerce') ,
				'VT' => __('Vermont', 'woocommerce') ,
				'VA' => __('Virginia', 'woocommerce') ,
				'WA' => __('Washington', 'woocommerce') ,
				'WV' => __('West Virginia', 'woocommerce') ,
				'WI' => __('Wisconsin', 'woocommerce') ,
				'WY' => __('Wyoming', 'woocommerce') 
			),
			'USAF' => array(
				'AA' => __('Americas', 'woocommerce') ,
				'AE' => __('Europe', 'woocommerce') ,
				'AP' => __('Pacific', 'woocommerce') 
			)
		);
	}

	/** get base country */
	function get_base_country() {
		$default = get_option('woocommerce_default_country');
		if (($pos = strpos($default, ':')) === false)
			return $default;
		return substr($default, 0, $pos);
	}

	/** get base state */
	function get_base_state() {
		$default = get_option('woocommerce_default_country');
		if (($pos = strrpos($default, ':')) === false)
			return '';
		return substr($default, $pos + 1);
	}

	/** get countries we allow only */
	function get_allowed_countries() {
	
		if (get_option('woocommerce_allowed_countries')!=='specific') return $this->countries;

		$allowed_countries = array();
		
		$allowed_countries_raw = get_option('woocommerce_specific_allowed_countries');
		
		foreach ($allowed_countries_raw as $country) :
			
			$allowed_countries[$country] = $this->countries[$country];
			
		endforeach;
		
		asort($allowed_countries);
		
		return $allowed_countries;
	}
	
	/** Gets an array of countries in the EU */
	function get_european_union_countries() {
		return array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK');
	}
	
	/** Gets the correct string for shipping - ether 'to the' or 'to' */
	function shipping_to_prefix() {
		global $woocommerce;
		$return = '';
		if (in_array($woocommerce->customer->get_shipping_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __('to the', 'woocommerce');
		else $return = __('to', 'woocommerce');
		return apply_filters('woocommerce_countries_shipping_to_prefix', $return, $woocommerce->customer->get_shipping_country());
	}
	
	/** Prefix certain countries with 'the' */
	function estimated_for_prefix() {
		$return = '';
		if (in_array($this->get_base_country(), array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' ))) $return = __('the', 'woocommerce') . ' ';
		return apply_filters('woocommerce_countries_estimated_for_prefix', $return, $this->get_base_country());
	}
	
	/** Correctly name tax in some countries VAT on the frontend */
	function tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __('VAT', 'woocommerce') : __('Tax', 'woocommerce');
		
		return apply_filters('woocommerce_countries_tax_or_vat', $return);
	}
	
	function inc_tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __('(incl. VAT)', 'woocommerce') : __('(incl. tax)', 'woocommerce');
		
		return apply_filters('woocommerce_countries_inc_tax_or_vat', $return);
	}
	
	function ex_tax_or_vat() {
		$return = ( in_array($this->get_base_country(), $this->get_european_union_countries()) ) ? __('(ex. VAT)', 'woocommerce') : __('(ex. tax)', 'woocommerce');
		
		return apply_filters('woocommerce_countries_ex_tax_or_vat', $return);
	}
	
	/** get states */
	function get_states( $cc ) {
		if (isset( $this->states[$cc] )) return $this->states[$cc];
	}
	
	/** Outputs the list of countries and states for use in dropdown boxes */
	function country_dropdown_options( $selected_country = '', $selected_state = '', $escape=false ) {
		
		asort($this->countries);
		
		if ( $this->countries ) foreach ( $this->countries as $key=>$value) :
			if ( $states =  $this->get_states($key) ) :
				echo '<optgroup label="'.$value.'">';
    				foreach ($states as $state_key=>$state_value) :
    					echo '<option value="'.$key.':'.$state_key.'"';
    					
    					if ($selected_country==$key && $selected_state==$state_key) echo ' selected="selected"';
    					
    					echo '>'.$value.' &mdash; '. ($escape ? esc_js($state_value) : $state_value) .'</option>';
    				endforeach;
    			echo '</optgroup>';
			else :
    			echo '<option';
    			if ($selected_country==$key && $selected_state=='*') echo ' selected="selected"';
    			echo ' value="'.$key.'">'. ($escape ? esc_js( __($value, 'woocommerce') ) : __($value, 'woocommerce') ) .'</option>';
			endif;
		endforeach;
	}
	
	/** Outputs the list of countries and states for use in multiselect boxes */
	function country_multiselect_options( $selected_countries = '', $escape=false ) {
		
		asort($this->countries);
		
		if ( $this->countries ) foreach ( $this->countries as $key=>$value) :
			if ( $states =  $this->get_states($key) ) :
				echo '<optgroup label="'.$value.'">';
    				foreach ($states as $state_key=>$state_value) :
    					echo '<option value="'.$key.':'.$state_key.'"';
  
    					if (isset($selected_countries[$key]) && in_array($state_key, $selected_countries[$key])) echo ' selected="selected"';
    					
    					echo '>' . ($escape ? esc_js($state_value) : $state_value) .'</option>';
    				endforeach;
    			echo '</optgroup>';
			else :
    			echo '<option';
    			
    			if (isset($selected_countries[$key]) && in_array('*', $selected_countries[$key])) echo ' selected="selected"';
    			
    			echo ' value="'.$key.'">'. ($escape ? esc_js( __($value, 'woocommerce') ) : __($value, 'woocommerce') ) .'</option>';
			endif;
		endforeach;
	}
	
	/** Get country address formats */
	function get_address_formats() {
		
		if (!$this->address_formats) :
			
			// Common formats
			$postcode_before_city = "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}";
			
			// Define address formats
			$this->address_formats = apply_filters('woocommerce_localisation_address_formats', array(
				'default' => "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}",
				'AU' => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
				'AT' => $postcode_before_city,
				'BE' => $postcode_before_city,
				'CN' => "{country} {postcode}\n{state}, {city}, {address_2}, {address_1}\n{company}\n{name}",
				'CZ' => $postcode_before_city,
				'DE' => $postcode_before_city,
				'FI' => $postcode_before_city,
				'DK' => $postcode_before_city,
				'FR' => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city_upper}\n{country}",
				'HK' => "{company}\n{first_name} {last_name_upper}\n{address_1}\n{address_2}\n{city_upper}\n{state_upper}\n{country}",
				'HU' => "{name}\n{company}\n{city}\n{address_1}\n{address_2}\n{postcode}\n{country}",
				'IS' => $postcode_before_city,
				'IS' => $postcode_before_city,
				'NL' => $postcode_before_city,
				'NZ' => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {postcode}\n{country}",
				'NO' => $postcode_before_city,
				'PL' => $postcode_before_city,
				'SK' => $postcode_before_city,
				'SI' => $postcode_before_city,
				'ES' => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city}\n{state}\n{country}",
				'SE' => $postcode_before_city,
				'TR' => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city} {state}\n{country}",
				'US' => "{name}\n{company}\n{address_1}\n{address_2}\n{city}, {state} {postcode}\n{country}",
			));
		endif;
		
		return $this->address_formats;
	}
	
	/** Get country address formats */
	function get_formatted_address( $args = array() ) {
	
		$args = array_map('trim', $args);
					
		extract( $args );
		
		// Get all formats
		$formats 		= $this->get_address_formats();
		
		// Get format for the address' country
		$format			= ($country && isset($formats[$country])) ? $formats[$country] : $formats['default'];
		
		// Handle full country name
		$full_country 	= (isset($this->countries[$country])) ? $this->countries[$country] : $country;
		
		// Country is not needed if the same as base
		if ($country==$this->get_base_country()) $format = str_replace('{country}', '', $format);
		
		// Handle full state name
		$full_state		= ($country && $state && isset($this->states[$country][$state])) ? $this->states[$country][$state] : $state;
		
		// Substitute address parts into the string
		$replace = apply_filters('woocommerce_formatted_address_replacements', array(
			'{first_name}'       => $first_name,
			'{last_name}'        => $last_name,
			'{name}'             => $first_name . ' ' . $last_name,
			'{company}'          => $company,
			'{address_1}'        => $address_1,
			'{address_2}'        => $address_2,
			'{city}'             => $city,
			'{state}'            => $full_state,
			'{postcode}'         => $postcode,
			'{country}'          => $full_country,
			'{first_name_upper}' => strtoupper($first_name),
			'{last_name_upper}'  => strtoupper($last_name),
			'{name_upper}'       => strtoupper($first_name . ' ' . $last_name),
			'{company_upper}'    => strtoupper($company),
			'{address_1_upper}'  => strtoupper($address_1),
			'{address_2_upper}'  => strtoupper($address_2),
			'{city_upper}'       => strtoupper($city),
			'{state_upper}'      => strtoupper($full_state),
			'{postcode_upper}'   => strtoupper($postcode),
			'{country_upper}'    => strtoupper($full_country),
		));

		$formatted_address = str_replace( array_keys($replace), $replace, $format );
		
		// Clean up white space
		$formatted_address = preg_replace('/  +/', ' ', trim($formatted_address));
		$formatted_address = preg_replace('/\n\n+/', "\n", $formatted_address);
		
		// Add html breaks
		$formatted_address = nl2br($formatted_address);
		
		// We're done!
		return $formatted_address;
	}
	
	/** Get country locale settings */
	function get_country_locale() {
		if (!$this->locale) :
		
			// Locale information used by the checkout
			$this->locale = apply_filters('woocommerce_localisation_address_fields', array(
				'AT' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'CA' => array(
					'state'	=> array(
						'label'	=> __('Province', 'woocommerce')
					)
				),
				'CL' => array(
					'city'		=> array(
						'required' 	=> false,
					),
					'state'		=> array(
						'label'		=> __('Municipality', 'woocommerce')
					)
				),
				'CN' => array(
					'state'	=> array(
						'label'	=> __('Province', 'woocommerce')
					)
				),
				'CZ' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'DE' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'DK' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'FI' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'FR' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'HK' => array(
					'postcode'	=> array(
						'required' => false
					),
					'city'	=> array(
						'label'	=> __('Town/District', 'woocommerce')
					),
					'state'		=> array(
						'label' => __('Region', 'woocommerce')
					)
				),
				'HU' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'IS' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'IL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'NL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'NZ' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'NO' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'PL' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'RO' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'SG' => array(
					'state'		=> array(
						'required' => false
					)
				),
				'SK' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'SI' => array(
					'postcode_before_city' => true,
					'state'		=> array(
						'required' => false
					)
				),
				'ES' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'label'	=> __('Province', 'woocommerce')
					)
				),
				'LK' => array(
					'state'	=> array(
						'required' => false
					)
				),
				'SE' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'required' => false
					)
				),
				'TR' => array(
					'postcode_before_city' => true,
					'state'	=> array(
						'label'	=> __('Province', 'woocommerce')
					)
				),
				'US' => array(
					'postcode'	=> array(
						'label' => __('Zip', 'woocommerce')
					),
					'state'		=> array(
						'label' => __('State', 'woocommerce')
					)
				),
				'GB' => array(
					'postcode'	=> array(
						'label' => __('Postcode', 'woocommerce')
					),
					'state'		=> array(
						'label' => __('County', 'woocommerce')
					)
				),
						
			));
			
			$this->locale = array_intersect_key($this->locale, $this->get_allowed_countries());
			
			$this->locale['default'] = apply_filters('woocommerce_localisation_default_address_fields', array(
				'postcode'	=> array(
					'label' => __('Postcode/Zip', 'woocommerce')
				),
				'city'	=> array(
					'label'	=> __('Town/City', 'woocommerce')
				),
				'state'		=> array(
					'label' => __('State/County', 'woocommerce')
				)
			));
			
		endif;
		
		return $this->locale;
		
	}
	
	/** Apply locale and get address fields */
	function get_address_fields( $country, $type = 'billing_' ) {
		$locale		= $this->get_country_locale();
		
		$fields = array(
			'first_name' => array( 
				'label' 		=> __('First Name', 'woocommerce'), 
				'required' 		=> true, 
				'class'			=> array('form-row-first'),
				),
			'last_name' => array( 
				'label' 		=> __('Last Name', 'woocommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last'),
				'clear'			=> true
				),
			'company' 	=> array( 
				'label' 		=> __('Company Name', 'woocommerce'), 
				'placeholder' 	=> __('Company (optional)', 'woocommerce'),
				'clear'			=> true
				),
			'address_1' 	=> array( 
				'label' 		=> __('Address', 'woocommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first'),
				),
			'address_2' => array( 
				'label' 		=> __('Address 2', 'woocommerce'), 
				'placeholder' 	=> __('Address 2 (optional)', 'woocommerce'), 
				'class' 		=> array('form-row-last'), 
				'label_class' 	=> array('hidden'),
				'clear'			=> true
				),
			'city' 		=> array( 
				'label' 		=> __('Town/City', 'woocommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first'),
				),
			'postcode' 	=> array( 
				'label' 		=> __('Postcode/Zip', 'woocommerce'), 
				'required' 		=> true, 
				'class'			=> array('form-row-last', 'update_totals_on_change'),
				'clear'			=> true
				),
			'country' 	=> array( 
				'type'			=> 'country', 
				'label' 		=> __('Country', 'woocommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first', 'update_totals_on_change', 'country_select'),
				),
			'state' 	=> array( 
				'type'			=> 'state', 
				'label' 		=> __('State/County', 'woocommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last', 'update_totals_on_change'),
				'clear'			=> true
				)
		);

		if (isset($locale[$country])) :
			
			$fields = woocommerce_array_overlay( $fields, $locale[$country] );
			
			if (isset($locale[$country]['postcode_before_city'])) :
				$fields['city']['class'] = array('form-row-last');
				$fields['postcode']['class'] = array('form-row-first', 'update_totals_on_change');
			endif;
			
		endif;
		
		// Prepend field keys
		$address_fields = array();
		
		foreach ($fields as $key => $value) :
			$address_fields[$type . $key] = $value;
		endforeach;
		
		// Billing/Shipping Specific
		if ($type=='billing_') :

			$address_fields['billing_email'] = array(
				'label' 		=> __('Email Address', 'woocommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-first')
			);	
			$address_fields['billing_phone'] = array(
				'label' 		=> __('Phone', 'woocommerce'), 
				'required' 		=> true, 
				'class' 		=> array('form-row-last'),
				'clear'			=> true
			);
			
			$address_fields = apply_filters('woocommerce_billing_fields', $address_fields);
		else :
			$address_fields = apply_filters('woocommerce_shipping_fields', $address_fields);
		endif;
		
		// Return
		return $address_fields;
	}
}

/** Depreciated */
class woocommerce_countries extends WC_Countries {
	public function __construct() { 
		_deprecated_function( 'woocommerce_countries', '1.4', 'WC_Countries()' );
		parent::__construct(); 
	} 
}