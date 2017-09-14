<?php

/*------------------------------------------------------------------------
# plg_contentstats_com_muscol - Content Statistics Music Collection extension plugin
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2011 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlacontentstatistics.com
# Technical Support:	Forum - http://www.joomlacontentstatistics.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class  plgContentstatsCom_muscol extends JPlugin
{
	var $component = 'com_muscol';
	var $component_name = 'Music Collection';

	function plgContentstatsCom_muscol(& $subject, $config)
	{
		parent::__construct($subject, $config);

	}

	function registerStatistic()
	{
		$view = JRequest::getString('view');
		$task = JRequest::getString('task');
		$id = JRequest::getInt('id');
		$layout = JRequest::getString('layout');
		
		$data = array();
		
		$data['reference_id'] = $id ;
		
		// we have to set this to TRUE if we want the statistic to be recorded.
		$data['register'] = false ;
		
		if($layout == "form") $register = false ;
		else $register = true ;
		
		switch($view){
			
			case "album":
				if($this->params->get('registeralbumviews') && $register){
					$data['type'] = 1 ;
					$data['register'] = true ;
				}
			break;
			
			case "artist": case "songs":
				if($this->params->get('registerartistviews') && $register){
					$data['type'] = 2 ;
					$data['register'] = true ;
				}
			break;
			
			case "song":
				if($this->params->get('registersongviews') && $register){
					$data['type'] = 3 ;
					$data['register'] = true ;
				}
			break;
			
			case "playlist":
				if($this->params->get('registerplaylistviews') && $register){
					$data['type'] = 5 ;
					$data['register'] = true ;
				}
			break;
			
			case "file":
				if($this->params->get('registersongdownloads') && $register){
					$data['type'] = 6 ;
					$data['register'] = true ;
				}
			break;
			
				
		}
		
		switch($task){
			
			case "add_song_play_count":
				if($this->params->get('registersongplays')){
					$data['type'] = 4 ;
					$data['register'] = true ;
				}
			break;
			
			case "rate":
				if($this->params->get('albumratings')){
					$data['type'] = 7 ;
					$data['reference_id'] = JRequest::getInt('album_id') ;
					$data['value'] = JRequest::getInt('points') ;
					$data['register'] = true ;
				}
			break;
			
			case "rate_song":
				if($this->params->get('songratings')){
					$data['type'] = 8 ;
					$data['reference_id'] = JRequest::getInt('album_id') ;
					$data['value'] = JRequest::getInt('points') ;
					$data['register'] = true ;
				}
			break;
		
		}
		
		$data['component'] = $this->component ;
		
		return $data;
		
	}
	
	function getRankingCompatibility()
	{
		$return->value = $this->component ;
		$return->text = $this->component_name . " (" . $this->component .")" ;
		
		return $return;
	}
	
	function getEvolutionCompatibility()
	{
		$return->value = $this->component ;
		$return->text = $this->component_name . " (" . $this->component .")" ;
		
		return $return;
	}
	
	function getCriteriaRanking()
	{
			
		$criteria[0]->value = 1 ;
		$criteria[0]->text = "Top viewed Albums" ;
		
		$criteria[1]->value = 2 ;
		$criteria[1]->text = "Top viewed Artists" ;
		
		$criteria[2]->value = 3 ;
		$criteria[2]->text = "Top viewed Songs" ;
		
		$criteria[3]->value = 4 ;
		$criteria[3]->text = "Top played Songs" ;
		
		$criteria[4]->value = 5 ;
		$criteria[4]->text = "Top viewed Playlists" ;
		
		$criteria[5]->value = 6 ;
		$criteria[5]->text = "Top donwloaded Songs" ;
		
		$criteria[6]->value = 7 ;
		$criteria[6]->text = "Top rated Albums" ;
		
		$criteria[7]->value = 8 ;
		$criteria[7]->text = "Top rated Songs" ;
		
		$return->options = $criteria ;
		$return->component = $this->component ;
		
		return $return ;
		
	}
	
	function getCriteriaEvolution()
	{
			
		$criteria[0]->value = 2 ;
		$criteria[0]->text = "Artist views" ;
		
		$criteria[1]->value = "artist_albums" ;
		$criteria[1]->text = "Albums belonging to ARTIST views" ;
		
		$criteria[2]->value = "artist_songs" ;
		$criteria[2]->text = "Songs belonging to ARTIST views" ;
		
		$criteria[3]->value = "artist_songs_plays" ;
		$criteria[3]->text = "Songs belonging to ARTIST plays" ;
		
		$criteria[4]->value = 1 ;
		$criteria[4]->text = "Album views" ;
		
		$criteria[5]->value = "album_songs" ;
		$criteria[5]->text = "Songs belonging to ALBUM views" ;
		
		$criteria[6]->value = "album_songs_plays" ;
		$criteria[6]->text = "Songs belonging to ALBUM plays" ;
		
		$criteria[7]->value = 3 ;
		$criteria[7]->text = "Song views" ;
		
		$criteria[8]->value = 4 ;
		$criteria[8]->text = "Song plays" ;
		
		$criteria[9]->value = 5 ;
		$criteria[9]->text = "Playlist views" ;
		
		$criteria[10]->value = 6 ;
		$criteria[10]->text = "Song downloads" ;
		
		$return->options = $criteria ;
		$return->component = $this->component ;
		
		return $return ;
		
	}
	
	
	function getSelectorsRanking()
	{
			
		$selectors[0]->value = "current_artist" ;
		$selectors[0]->text = "CURRENT artist (if currently viewing an ARTIST, ALBUM or SONG)" ;
		
		$selectors[1]->value = "current_album" ;
		$selectors[1]->text = "CURRENT album (if currently viewing an ALBUM or SONG)" ;
		
		$selectors[2]->value = "specific_artist" ;
		$selectors[2]->text = "SPECIFIC artist (fill the next field with the correct ID!!!)" ;
		
		$selectors[3]->value = "specific_album" ;
		$selectors[3]->text = "SPECIFIC album (fill the next field with the correct ID!!!)" ;
		
		$selectors[4]->value = "all" ;
		$selectors[4]->text = "ALL artist/album/song (equivalent to NO FILTER)" ;
		
		$return->options = $selectors ;
		$return->component = $this->component ;
		
		return $return ;
		
	}
	
	function getSelectorsEvolution()
	{
			
		$selectors[0]->value = "current" ;
		$selectors[0]->text = "CURRENT artist/album/song/playlist being showed" ;
		
		$selectors[1]->value = "specific" ;
		$selectors[1]->text = "SPECIFIC artist/album/song/playlist (fill the next field with the correct ID!!!)" ;
		
		$selectors[2]->value = "all" ;
		$selectors[2]->text = "ALL artist/album/song/playlist (equivalent to NO FILTER)" ;
		
		$return->options = $selectors ;
		$return->component = $this->component ;
		
		return $return ;
		
	}
	
	function getQueryRanking($criteria, $selector, $specific_id, $where_clause, $params){
		
		$query = '' ;
		
		$db =& JFactory::getDBO();
		
		$view = JRequest::getVar('view');
		$id = JRequest::getInt('id');
		
		// artist_id is for everything, actually: artist, album, song
		if($specific_id){
			$reference_id = $specific_id;
			$artist_id = $reference_id ;
			$album_id = $reference_id ;
		
		}
		else{
			
			switch($view){
				case 'artist': case 'songs': // we are viewing the ARTIST page
					$artist_id = $id ;
					
				break;
				
				case 'album': // we are viewing the ALBUM page
					
					$album_id = $id ;
					
					$query = ' SELECT artist_id FROM #__muscol_albums WHERE id = ' .$id ;
					$db->setQuery($query);
					$artist_id = $db->loadResult();
					
				break;
				
				case 'song': // we are viewing the SONG page
					
					$query = ' SELECT artist_id FROM #__muscol_songs WHERE id = ' .$id ;
					$db->setQuery($query);
					$artist_id = $db->loadResult();
					
					$query = ' SELECT album_id FROM #__muscol_songs WHERE id = ' .$id ;
					$db->setQuery($query);
					$album_id = $db->loadResult();
					
				break;
			}
			
		}
				
		switch($criteria){
			case 1: case 7:// albums viewed
			
				switch($selector){
					case 'current_artist': case 'specific_artist':
						$where_selector = ' AND ar.id = ' . $artist_id ;
					break;
				}
			
				$query = 	' SELECT al.*, COUNT(st.id) AS howmuch, al.name as item_name, ar.artist_name, ' .
							' CONCAT("index.php?option=com_muscol&view=album&id=", al.id) as item_link ' .
							' FROM #__content_statistics as st '.
							' LEFT JOIN #__muscol_albums AS al ON al.id = st.reference_id ' .
							' LEFT JOIN #__muscol_artists AS ar ON ar.id = al.artist_id ' .
							' WHERE st.type = ' . $criteria . 
							' AND al.id != 0 ' .
							$where_clause .
							$where_selector .
							' GROUP BY al.id ' .
							' ORDER BY howmuch DESC '
							;
			
			break;
			
			case 2: // artists viewed
			
				$query = 	' SELECT ar.*, COUNT(st.id) AS howmuch, ar.artist_name as item_name, '.
							' CONCAT("index.php?option=com_muscol&view=artist&id=", ar.id) as item_link ' .
							' FROM #__content_statistics as st '.
							' LEFT JOIN #__muscol_artists AS ar ON ar.id = st.reference_id ' .
							' WHERE st.type = ' . $criteria . 
							' AND ar.id != 0 ' .
							$where_clause .
							' GROUP BY ar.id ' .
							' ORDER BY howmuch DESC '
							;
			
			break;
			
			case 3: case 4: case 6: case 8:// songs viewed OR played OR downloaded
			
				switch($selector){
					case 'current_artist': case 'specific_artist':
						$where_selector = ' AND ar.id = ' . $artist_id ;
					break;
					case 'current_album': case 'specific_album':
						$where_selector = ' AND al.id = ' . $album_id ;
					break;
				}
				
				$query = 	' SELECT s.*, COUNT(st.id) AS howmuch, s.name as item_name, ar.artist_name , '.
							' CONCAT("index.php?option=com_muscol&view=song&id=", s.id) as item_link ' .
							' FROM #__content_statistics as st '.
							' LEFT JOIN #__muscol_songs AS s ON s.id = st.reference_id ' .
							' LEFT JOIN #__muscol_albums AS al ON al.id = s.album_id ' .
							' LEFT JOIN #__muscol_artists AS ar ON ar.id = s.artist_id ' .
							' WHERE st.type = ' . $criteria . 
							' AND s.id != 0 ' .
							$where_clause .
							$where_selector .
							' GROUP BY s.id ' .
							' ORDER BY howmuch DESC '
							;
			
			break;
			
			case 5: // playlists viewed
				
				$query = 	' SELECT pl.*, COUNT(st.id) AS howmuch, pl.title as item_name, '.
							' CONCAT("index.php?option=com_muscol&view=playlist&id=", pl.id) as item_link ' .
							' FROM #__content_statistics as st '.
							' LEFT JOIN #__muscol_playlists AS pl ON pl.id = st.reference_id ' .
							' WHERE st.type = ' . $criteria . 
							' AND pl.id != 0 ' .
							$where_clause .
							$where_selector .
							' GROUP BY pl.id ' .
							' ORDER BY howmuch DESC '
							;
			
			break;
			
			
		}
		//echo $query;die;
		//do not change this
		$return->query = $query ;
		$return->component = $this->component ;
		
		return $return ;
		
	}
	
	function getQueryEvolution($criteria, $selector, $specific_id, $where_clause, $params){
		
		$query = '' ;
		
		$db =& JFactory::getDBO();
		
		$view = JRequest::getVar('view');
		$id = JRequest::getInt('id');
		
		if(!$specific_id){
			
			switch($view){
				case 'artist': case 'songs': // we are viewing the ARTIST page
					switch($criteria){
						case 2: case 'artist_albums': case 'artist_songs':case 'artist_songs_plays':
							$specific_id = $id ;
							$query2 = ' SELECT artist_name FROM #__muscol_artists WHERE id = ' .$id ;
						break;
					}
					
				break;
				
				case 'album': // we are viewing the ALBUM page
					switch($criteria){
						case 1: case 'album_songs': case 'album_songs_plays':
							$specific_id = $id ;
							$query2 = 	' SELECT al.name as album_name, ar.artist_name FROM #__muscol_albums as al '.
										' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id WHERE al.id = ' .$id ;
						break;
						case 2: case 'artist_albums': case 'artist_songs':case 'artist_songs_plays':
							$query = 	' SELECT al.artist_id as reference_id, al.name as album_name FROM #__muscol_albums as al '.
										' LEFT JOIN #__muscol_artists as ar ON ar.id = al.artist_id WHERE al.id = ' .$id ;
						break;
					}
					
				break;
				
				case 'song': // we are viewing the SONG page
					switch($criteria){
						case 3: case 4:
							$specific_id = $id ;
							$query2 = 	' SELECT s.name as song_name, ar.artist_name, al.name as album_name FROM #__muscol_songs as s '.
										' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' . 
										' LEFT JOIN #__muscol_artists as ar ON ar.id = s.artist_id WHERE s.id = ' .$id ;
						break;
						case 1: case 'album_songs': case 'album_songs_plays':
							$query = 	' SELECT s.album_id as reference_id, s.name as song_name, ar.artist_name, al.name as album_name FROM #__muscol_songs as s ' .
										' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' . 
										' LEFT JOIN #__muscol_artists as ar ON ar.id = s.artist_id WHERE s.id = ' .$id ;
						break;
						case 2: case 'artist_albums': case 'artist_songs':case 'artist_songs_plays':
							$query = 	' SELECT s.artist_id as reference_id, s.name as song_name, ar.artist_name, al.name as album_name FROM #__muscol_songs as s ' .
										' LEFT JOIN #__muscol_albums as al ON al.id = s.album_id ' . 
										' LEFT JOIN #__muscol_artists as ar ON ar.id = s.artist_id WHERE s.id = ' .$id ;
						break;
					}
					
				break;
				
				case 'playlist': // we are viewing the PLAYLIST page
					switch($criteria){
						case 5:
							$specific_id = $id ;
							$query2 = ' SELECT title as playlist_name FROM #__muscol_playlists WHERE id = ' .$id ;
						break;
					}
					
				break;
			}
			
			if($query != ''){
				$db->setQuery($query) ;
				$result = $db->loadObject();
				$specific_id = $result->reference_id;
				
				$names = $result ;
			}
			
			if($query2 != ''){
				$db->setQuery($query2) ;
				$names = $db->loadObject();
			}
			
			//print_r(str_replace("#_","jos",$query));echo "\n";
			//print_r(str_replace("#_","jos",$query2));echo "\n";
			
		}
		
		$query = '' ;
		
		switch($criteria){
			case 2: case 1: case 3: case 4: case 5: case 6: case 7: case 8:
			
				if($selector != "all") $where_selector = ' AND st.reference_id = ' . $specific_id ;
				else $where_selector = "";
			
				$query = 	' SELECT COUNT(st.id) AS howmuch, st.date_event  FROM #__content_statistics as st '.
							' WHERE st.type = ' . $criteria .
							$where_selector .
							$where_clause
							;
				
			break;
			
			case 'artist_albums':
			
				if($selector != "all") $where_selector = ' AND al.artist_id = ' . $specific_id ;
				else $where_selector = "";
			
				$query = 	' SELECT COUNT(st.id) AS howmuch, st.date_event  FROM #__content_statistics as st '.
							' LEFT JOIN #__muscol_albums as al ON al.id = st.reference_id ' .
							' WHERE st.type = 1 ' .
							$where_selector .
							$where_clause
							;
				
			break;
			
			case 'album_songs': case 'album_songs_plays':
			
				if($criteria == 'album_songs') $type = '3' ;
				elseif($criteria == 'album_songs_plays') $type = '4' ;
			
				if($selector != "all") $where_selector = ' AND s.album_id = ' . $specific_id ;
				else $where_selector = "";
			
				$query = 	' SELECT COUNT(st.id) AS howmuch, st.date_event  FROM #__content_statistics as st '.
							' LEFT JOIN #__muscol_songs as s ON s.id = st.reference_id ' .
							' WHERE st.type = ' . $type .
							$where_selector .
							$where_clause
							;
				
				
			break;
			
			case 'artist_songs':case 'artist_songs_plays':
			
				if($criteria == 'artist_songs') $type = '3' ;
				elseif($criteria == 'artist_songs_plays') $type = '4' ;
			
				if($selector != "all") $where_selector = ' AND s.artist_id = ' . $specific_id ;
				else $where_selector = "";
			
				$query = 	' SELECT COUNT(st.id) AS howmuch, st.date_event  FROM #__content_statistics as st '.
							' LEFT JOIN #__muscol_songs as s ON s.id = st.reference_id ' .
							' WHERE st.type = ' . $type .
							$where_selector .
							$where_clause
							;
				
			break;
			
		} // end switch
		
		
		//do not change this
		$return->query = $query ;
		$return->component = $this->component ;
		
		return $return ;
		
	}
	
	//new in version 1.3
	function registerStatistic_com_muscol(){
		return $this->registerStatistic() ;
	}
	
	function getQueryRanking_com_muscol($criteria, $selector, $specific_id, $where_clause, $params){
		return $this->getQueryRanking($criteria, $selector, $specific_id, $where_clause, $params) ;
	}
	
	function getQueryEvolution_com_muscol($criteria, $selector, $specific_id, $where_clause, $params){
		return $this->getQueryEvolution($criteria, $selector, $specific_id, $where_clause, $params) ;
	}
	
	//new for version 1.4
	function getTypes()
	{
		//load the translation
		$this->loadLanguage( );
		
		$criteria[1] = JText::_('ALBUM_VIEW') ;
		$criteria[2] = JText::_('ARTIST_VIEW') ;
		$criteria[3] = JText::_('SONG_VIEW') ;
		$criteria[4] = JText::_('SONG_PLAYED') ;
		$criteria[5] = JText::_('PLAYLIST_VIEW') ;
		$criteria[6] = JText::_('SONG_DOWNLOADED') ;
		$criteria[7] = JText::_('ALBUM_RATED') ;
		$criteria[8] = JText::_('SONG_RATED') ;
		
		//do not change this
		$return->options = $criteria ;
		$return->component = $this->component ;
		
		return $return ;
		
	}
	
	function getItemName_com_muscol($reference_id, $type, $entry_id)
	{
		switch($type){
			case 1: case 7: 
				$query = ' SELECT al.name as item_name ' .
							' FROM #__muscol_albums as al '.
							' WHERE al.id = ' . $reference_id ;
			break;
			case 2: 
				$query = ' SELECT ar.artist_name as item_name ' .
							' FROM #__muscol_artists as ar '.
							' WHERE ar.id = ' . $reference_id ;
			break;
			case 3:  case 4: case 6: case 8:
				$query = ' SELECT s.name as item_name ' .
							' FROM #__muscol_songs as s '.
							' WHERE s.id = ' . $reference_id ;
			break;
			case 5: 
				$query = ' SELECT pl.title as item_name ' .
							' FROM #__muscol_playlists as pl '.
							' WHERE pl.id = ' . $reference_id ;
			break;
		}
		
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$return = $db->loadResult();
		
		return $return ;
		
	}

}

