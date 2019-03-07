<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <2016>  <jamelbaz@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		class/myclass.class.php
 * 	\ingroup	mymodule
 * 	\brief		This file is an example CRUD class file (Create/Read/Update/Delete)
 * 				Put some comments here
 */
// Put here all includes required by your class file
//require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";
//require_once DOL_DOCUMENT_ROOT."/societe/class/societe.class.php";
//require_once DOL_DOCUMENT_ROOT."/product/class/product.class.php";

/**
 * Put your class' description here
 */
class Dolifullcalendar // extends CommonObject
{

	private $db; //!< To store db handler
	public $error; //!< To return error code (or message)
	public $errors = array(); //!< To return several error codes (or messages)
	//public $element='skeleton';	//!< Id that identify managed objects
	//public $table_element='skeleton';	//!< Name of table without prefix where object is stored
	public $id;
	public $title;
	public $datep;
	public $datep2;
	public $color;

	/**
	 * Constructor
	 *
	 * 	@param	DoliDb		$db		Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		return 1;
	}

	/**
	 * Create object into database
	 *
	 * 	@param		User	$user		User that create
	 * 	@param		int		$notrigger	0=launch triggers after, 1=disable triggers
	 * 	@return		int					<0 if KO, Id of created object if OK
	 */
	
	function add($user,$notrigger=0)
    {
        global $langs,$conf,$hookmanager;

        $error=0;
        $now=dol_now();

        // Clean parameters
        $this->title=dol_trunc(trim($this->title),128);
        //if (! empty($this->date)  && ! empty($this->dateend)) $this->durationa=($this->dateend - $this->date);
        if (! empty($this->start) && ! empty($this->end) && $this->start > $this->end) $this->end=$this->start;
        

        $this->db->begin();

        $sql = "INSERT INTO ".MAIN_DB_PREFIX."actioncomm";
        $sql.= "(label,";
        $sql.= "color,";
        $sql.= "datep,";
        $sql.= "datep2,";
        $sql.= "fk_action,";
        $sql.= "code";
        $sql.= ") VALUES (";
        $sql.= "'".$this->db->escape($this->title)."',";
		$sql.= "'".$this->db->escape($this->color)."',";
        $sql.= (strval($this->start)!=''?"'".$this->start."'":"null").",";
        $sql.= (strval($this->end)!=''?"'".$this->end."'":"null").",";
        $sql.= "50,";
        $sql.= "'AC_OTH'";
        
        $sql.= ")";
		//echo $sql;
        dol_syslog(get_class($this)."::add", LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."actioncomm","id");

            // Now insert assignedusers
			if (! $error)
			{

				$sql ="INSERT INTO ".MAIN_DB_PREFIX."actioncomm_resources(fk_actioncomm, element_type, fk_element, mandatory, transparency, answer_status)";
				$sql.=" VALUES(".$this->id.", 'user', ". $user->id .", 0, 0, 0)";
				//echo $sql;
				$resql = $this->db->query($sql);
				if (! $resql)
				{
					$error++;
					$this->errors[]=$this->db->lasterror();
				}
			}
			
			if (! $error)
            {
            	$this->db->commit();
            	return $this->id;
            }
           
        }
        else
        {
            $this->db->rollback();
            $this->error=$this->db->lasterror();
            return -1;
        }

    }

	/**
	 * Load object in memory from database
	 *
	 * 	@param		int		$id	Id object
	 * 	@return		int			<0 if KO, >0 if OK
	 */
    /**
     *    Load object from database
     *
     *    @param	int		$id     	Id of action to get
     *    @param	string	$ref    	Ref of action to get
     *    @param	string	$ref_ext	Ref ext to get
     *    @return	int					<0 if KO, >0 if OK
     */
    function fetchAll($year)
    {
		$this->attrExist();
		
        global $langs;

        $sql = "SELECT a.id,";
        $sql.= " a.label as title,";
        $sql.= " a.color as color,";
        $sql.= " a.datep as start,";
        $sql.= " a.datep2 as end";
        $sql.= " FROM ".MAIN_DB_PREFIX."actioncomm as a ";
        $sql.= " WHERE year(datep) = $year";
        //echo $sql;

        dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
        $resql=$this->db->query($sql);
		if ($resql) {
			$i = 0;
			$obj = '';
			$num = $this->db->num_rows($resql);
			$data = array();
			if ($num) {
				while ( $i < $num ) {
					
					$obj = $this->db->fetch_object($resql);
					
					$name_cat = $obj->name_cat;
					
					$data[$i] =	array(
									'id' => $obj->id,
									'start' => $obj->start,
									'end' => $obj->end,
									'title' => $obj->title,
									'color' => $obj->color,
									);
					
					$i ++;
				}
			}
			
			return $data;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(__METHOD__ . " " . $this->error, LOG_ERR);

			return -1;
		}

    }

	/**
	 * Update object into database
	 *
	 * 	@param		User	$user		User that modify
	 * 	@param		int		$notrigger	0=launch triggers after, 1=disable triggers
	 * 	@return		int					<0 if KO, >0 if OK
	 */
	public function updateTitle($user = 0, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		// Clean parameters
		if (isset($this->title)) {
			$this->title = trim($this->title);
		}
		if (isset($this->start)) {
			$this->datep = trim($this->start);
		}
		if (isset($this->end)) {
			$this->datep2 = trim($this->end);
		}
		if (isset($this->color)) {
			$this->color = trim($this->color);
		}

		// Check parameters
		// Put here code to add control on parameters values
		// Update request
		$sql = "UPDATE " . MAIN_DB_PREFIX . "actioncomm SET";
		$sql.= " label=" . (isset($this->title) ? "'" . $this->db->escape($this->title) . "'" : "null") . ",";		
		$sql.= " color=" . (isset($this->color) ? "'" . $this->db->escape($this->color) . "'" : "null") . "";

		$sql.= " WHERE id=" . $this->id;
		//echo $sql;
		$this->db->begin();

		dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error ++;
			$this->errors[] = "Error " . $this->db->lasterror();
		}


		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
				$this->error.=($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();

			return -1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}
	
	/**
	 * Update object into database
	 *
	 * 	@param		User	$user		User that modify
	 * 	@param		int		$notrigger	0=launch triggers after, 1=disable triggers
	 * 	@return		int					<0 if KO, >0 if OK
	 */
	public function updateDate($user = 0, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		// Clean parameters
		
		if (isset($this->start)) {
			$this->datep = trim($this->start);
		}
		if (isset($this->end)) {
			$this->datep2 = trim($this->end);
		}

		// Check parameters
		// Put here code to add control on parameters values
		// Update request
		$sql = "UPDATE " . MAIN_DB_PREFIX . "actioncomm SET";
		$sql.= " datep=" . (isset($this->start) ? "'" . $this->db->escape($this->start) . "'" : "null") . ",";
		$sql.= " datep2=" . (isset($this->end) ? "'" . $this->db->escape($this->end) . "'" : "null") . "";

		$sql.= " WHERE id=" . $this->id;

		$this->db->begin();

		dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error ++;
			$this->errors[] = "Error " . $this->db->lasterror();
		}


		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
				$this->error.=($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();

			return 0;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * 	@param		User	$user		User that delete
	 * 	@param		int		$notrigger	0=launch triggers after, 1=disable triggers
	 * 	@return		int					<0 if KO, >0 if OK
	 */
	public function delete($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		$this->db->begin();



		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "actioncomm";
			$sql.= " WHERE id=" . $this->id;

			dol_syslog(__METHOD__ . " sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
				$this->error.=($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();

			return -1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}
	
	public function attrExist()
	{
		$error = 0;
		
		$sql = "SHOW columns FROM ".MAIN_DB_PREFIX."actioncomm where field='color'";
        

       dol_syslog(__METHOD__ . " sql=" . $sql);
        $resql=$this->db->query($sql);
		
		if(empty($resql->num_rows)){ // si pas de champ j'ajout color à la table
			$sql = "ALTER TABLE ".MAIN_DB_PREFIX."actioncomm ADD color varchar(7) DEFAULT NULL";
			
			dol_syslog(__METHOD__ . " sql=" . $sql);
			$resql = $this->db->query($sql);
			
			if ($error) {
				
				$this->db->rollback();

			} else {
				$this->db->commit();

			}
			
		}
		return 1;
		
	}

}
