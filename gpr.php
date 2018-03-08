#!/usr/bin/php
<?php
/**
* the main game object for GraphPaperRPG!
**/
class GraphPaperRPG {
	/** @var int tracks the time of day, in a range from 0 to 72 (20 minute chunks of the day.   3 per hour, * 24 hours) 
   *
	* 0 20 40 60 20 40 60 20 40 60 20 40 60 20 40 60 20 40 60
	* 0       1        2        3        4        5
	* 0 1  2  3  4  5  6  7  8  9  10 11 12 13 14 15 16 17
	*/
	$timeTicks = 0;

	/** @var int tracks the number of days the game has been running **/
	$dayCount = 0;

	/**
	* create a new GraphPaperRPG object
	**/
	public function __construct() {
	}

	/**
	* returns the general time of day.  morning, afternoon, evening, or night
	*
	* @return string
	**/
	public function getDayPart() {
		$hour = $this->timeTicks / 3;

		if ($hour >= 6 && $hour <= 12) {
			/** 6am to noon **/
			$timeOfDay = "Morning";
		} else if ($hour > 12 && $hour <= 17) {
			/** after noon, up to 5pm **/
			$timeOfDay = "Afternoon";
		} else if ($hour > 17 && $hour <= 21) {
			/** after 5pm, to 9pm **/
			$timeOfDay = "Evening";
		} else {
			/** after 9pm, up until 6am **/
			$timeOfDay = "Night";
		}

		return $timeOfDay;
	}

	/**
	* returns the timeTick property
	*
	* @return integer
	**/
	private function getTimeTicks() {
		return $this->timeTicks;
	}

	/**
	* returns the dayCount property
	*
	* @return integer
	**/
	private function getDayCount() {
		return $this->dayCount;
	}

	/**
	* returns the number of days the game has been running.
	*
	* partial days are always rounded up...  so at game start, we're in day 1.  etc.
	*
	* @return integer
	**/
	public function getDays() {
		return $this->dayCount + 1;
	}

	/**
	* gets the hour of the current day
	*
	* @return integer
	**/
	public function getHour() {
		return floor($this->timeTicks / 3);

	}

	/**
	* increments the game time.   by a 20 minute segment!  (our range is zero to 72)
	**/
	public function bumpTimeTick() {
		$this->timeTicks++;

		// if we're at the end of the day, increment the day count and reset timeTicks
		if ($this->timeTicks >= 72) {
			$this->timeTicks = 0;
			$this->bumpDayCount();
		}
	}

	/**
	* increments the game time day counter
	**/
	public function bumpDayCount() {
		$this->dayCount++;
	}

	/**
	* the main cycle of gameplay!  in here endlessly until user quits, or gameover 
	**/
	public function mainLoop() {
		// NPC Moves
		// Handle morale adjustment
		// Check and resolve encounters
		// handle any special terrain 
		// Expose new fow squares
		// Wait a set amount of time
		// REPEAT 
	}
}

/**
* the map for the rpg
**/
class GameMap {
	/** @var integer the width of the map **/
	private $x = null;

	/** @var integer the height of the map **/
	private $y = null;

	/** @var array containing any terrain tiles assigned to the map grid **/
	private $gridTerrain = null;

	/** @var array sets encounter levels on the map grid **/
	private $gridEncounters = null;

	/** @var array tracks spaces revealed by the player (fog of war) **/
	private $gridFog = null;

	/** @var array special player modifiers associated to spaces on the map **/
	private $gridPlayerModifiers = null;
	
	/** @var array locations allowing placement of certain specials **/
	private $gridPlacementLocks = null;

	/** @var array location of impassable water flows **/
	private $gridStreams = null;

	/**
	* initialize the new gamemap
	*
	* @param integer $x width
	* @param integer $y height
	**/
	public function __construct($x = 10, $y = 10) {
		$this->_setX($x);
		$this->_setY($y);
	}

	/**
	* sets the width of the map
	*
	* @param integer $x width
	**/
	private function _setX($x) {
		$this->x = $x;
	}

	/**
	* gets the width of the map 
	*
	* @return integer
	**/
	private function _getX() {
		return $this->x;
	}

	/**
	* sets the height of the map
	*
	* @param integer $y
	**/
	private function _setY($y) {
		$this->y = $y;
	}

	/**
	* gets the height of the map
	*
	* @return integer
	**/
	private function _getY() {
		return $this->y;
	}
}

/**
* the computer controlled adventurer 
**/
class NPC {
	/** 
	* various player status that can be set in the $status variable
	**/
	/** @const the user has died! **/
	const PLAYER_STATUS_DEAD = 'dead';

	/** @const the user has given up and quit **/
	const PLAYER_STATUS_QUIT = 'quit';

	/** @var integer the life force of the npc!  when its gone, they are daed **/
	private $hitpoints = null;

	/** @var integer the total amount of hitpoints.  upper bound **/
	private $maxHitpoints = null;

	/** @var integer how vigorous or tired.   continually ticks down to zero **/
	private $endurance = null;

	/** @var integer keep the morale up, or the npc will give up and you lose **/
	private $morale = null;

	/** @var integer the total amount of morale.  upper bound **/
	private $maxMorale = null;

	/** @var integer smarter NPC's make better auto movements, among other things **/
	private $intelligence = null;

	/** @var integer how agile an npc is.  higher dex == less slowdowns on rough terrain **/
	private $dexterity = null;

	/** @var integer modifier used in combat **/
	private $strength = null;

	/** @var integer the distance a character can see (fog of war) **/
	private $sight = null;

	/** @var integer modifier used for chance calculations **/
	private $luck = null;

	/** @var integer general hardiness.  modifier to morale adjustments due to things like bad weather **/
	private $constitution = null;

	/** @var string the name of the npc **/
	private $name = null;

	/** @var array a collection of status indications.  (poisoned, cursed, dead, etc) **/
	private $status = null;

	/** @var integer a characters accumulation of wealth **/
	private $gold = 0;

	/** @var integer a modifier of random encounter rolls **/
	private $encounterModifier = null;

	/**
	* create a new npc object
	**/
	public function __construct() {

	}

	/**
	* sets the hitpoints property
	*
	* @param integer $val
	**/
	private function _setHitpoints($val) {
		$this->hitpoints = $val;
	}

	/**
	* returns the hitpoints property
	*
	* @return integer
	**/
	public function getHitpoints() {
		return $this->hitpoints;
	}

	/**
	* increases the hitpoints property, to a limit of maxHitpoints
	*
	* @param integer $hp the amount to increment
	**/	
	public function addHitpoints($hp) {
		$newHp = $this->getHitpoints() + $hp;
		$maxHp = $this->getMaxHitpoints();

		if ($newHp >= $maxHp) {
			$this->_setHitpoints($maxHp);
		} else {
			$this->_setHitpoints($newHp);
		}	
	}

	/**
	* decrements a users hitpoint count, and triggers a death event if reaches zero
	*
	* @param integer $hp the amount to decrement 
	**/
	public function removeHitpoints($hp) {
		$newHp = $this->getHitpoints() - $hp;

		if ($newHp <= 0) {
			// death!
			$this->_die();
		} else {
			$this->_setHitpoints($newHp);
		}
	}

	/**
	* sets the maxHitpoints property
	*
	* @param integer $val
	**/
	private function _setMaxHitpoints($val) {
		$this->maxHitpoints = $val;
	}

	/**
	* returns the maxHitpoints property
	*
	* @return integer
	**/
	public function getMaxHitpoints() {
		return $this->maxHitpoints;
	}

	/**
	* sets the endurance property
	*
	* @param integer $val
	**/
	private function _setEndurance($val) {
		$this->endurance = $val;
	}

	/**
	* returns the endurance property
	*
	* @return integer
	**/
	public function getEndurance() {
		return $this->endurance;
	}
	
	/**
	* sets the morale property
	*
	* @param integer $val
	**/
	private function _setMorale($val) {
		$this->morale = $val;
	}

	/**
	* returns the morale property
	*
	* @return integer
	**/
	public function getMorale() {
		return $this->morale;
	}
	
	/**
	* increases the morale property, to a limit of maxMorale
	*
	* @param integer $m the amount to increment
	**/	
	public function addMorale($m) {
		$newMorale = $this->getMorale() + $m;
		$maxMorale = $this->getMaxMorale();

		if ($newMorale >= $maxMorale) {
			$this->_setMorale($maxMorale);
		} else {
			$this->_setMorale($newMorale);
		}	
	}

	/**
	* decrements a users morale count, and triggers a quit event if reaches zero
	*
	* @param integer $m the amount to decrement 
	**/
	public function removeMorale($m) {
		$newMorale = $this->getMorale() - $m;

		if ($newMorale <= 0) {
			// quit!
			$this->_quit();
		} else {
			$this->_setMorale($newMorale);
		}
	}

	/**
	* sets the maxMorale property
	*
	* @param integer $val
	**/
	private function _setMaxMorale($val) {
		$this->maxMorale = $val;
	}

	/**
	* returns the maxMorale property
	*
	* @return integer
	**/
	public function getMaxMorale() {
		return $this->maxMorale;
	}
	
	/**
	* sets the intelligence property
	*
	* @param integer $val
	**/
	private function _setIntelligence($val) {
		$this->intelligence = $val;
	}

	/**
	* returns the intelligence property
	*
	* @return integer
	**/
	public function getIntelligence() {
		return $this->intelligence;
	}
	
	/**
	* sets the dexterity property
	*
	* @param integer $val
	**/
	private function _setDexterity($val) {
		$this->dexterity = $val;
	}

	/**
	* returns the dexterity property
	*
	* @return integer
	**/
	public function getDexterity() {
		return $this->dexterity;
	}
	
	/**
	* sets the strength property
	*
	* @param integer $val
	**/
	private function _setStrength($val) {
		$this->strength = $val;
	}

	/**
	* returns the strength property
	*
	* @return integer
	**/
	public function getStrength() {
		return $this->strength;
	}
	
	/**
	* sets the sight property
	*
	* @param integer $val
	**/
	private function _setSight($val) {
		$this->sight = $val;
	}

	/**
	* returns the sight property
	*
	* @return integer
	**/
	public function getSight() {
		return $this->sight;
	}
	
	/**
	* sets the luck property
	*
	* @param integer $val
	**/
	private function _setLuck($val) {
		$this->luck = $val;
	}

	/**
	* returns the luck property
	*
	* @return integer
	**/
	public function getLuck() {
		return $this->luck;
	}
	
	/**
	* sets the constitution property
	*
	* @param integer $val
	**/
	private function _setConstitution($val) {
		$this->constitution = $val;
	}

	/**
	* returns the constitution property
	*
	* @return integer
	**/
	public function getConstitution() {
		return $this->constitution;
	}
	
	/**
	* sets the name property
	*
	* @param string $name
	**/
	private function _setName($name) {
		$this->name = $name;
	}

	/**
	* returns the name property
	*
	* @return string
	**/
	public function getName() {
		return $this->name;
	}

	/**
	* adds the specified amount of gold
	*
	* @param integer $amount
	* @return true
	**/
	public function addGold($amount) {
		$this->_setGold($this->getGold() + $amount);

		// returns a boolean to be consistent with removeGold
		return true;
	}

	/**
	* decrements a users gold by the given amount (if they have it!)
	*
	* @param integer $amount
	* @return boolean indicating whether the required amount of gold was present and successfully deducted 
	**/
	public function removeGold($amount) {
		$gold = $this->getGold();

		if ($gold >= $amount) {
			$this->_setGold($gold - $amount);

			return true;
		}

		return false;
	}

	/**
	* sets the gold amount
	*
	* @param integer $gold
	**/
	private function _setGold($gold) {
		$this->gold = $gold;
	}

	/**
	* returns the gold property
	*
	* @return integer
	**/
	public function getGold() {
		return $this->gold;
	}

	/**
	* sets the encounterModifier property
	*
	* @param integer $mod
	**/
	private function _setEncounterModifier($mod) {
		$this->encounterModifier = $mod;
	}

	/**
	* gets the encounterModifier property
	*
	* @return null|integer
	**/
	public function getEncounterModifier() {
		return $this->encounterModifier;
	}

	/**
	* collection of routines to trigger when a player dies! (hp <= 0)
	**/
	private function _die() {
	}

	/**
	* collection of routines to trigger when a player runs out of morale and quits (morale <= 0)
	**/
	private function _quit() {
	}
}

/**
* represents the stream of cards the player picks to fill the map
**/
class CardStream {
	public function __construct() {
	}
}

/**
* represents the person playing the game
**/
class Player {
	/** @var array a set of cards the player has pulled out of the stream **/
	private $cardBank = null;

	/** @var string the players name **/
	private $name = null;

	/**
	* create a new player object
	*
	* @param string $name the person playing gives us a name!
	**/
	public function __construct($name = "Player") {
		$this->_setName($name);
	}

	/**
	* returns the cardBank property
	*
	* @return null|array
	**/
	public function getCardBank() {
		return $this->cardBank;
	}

	/**
	* sets the name property
	*
	* @param string $name
	**/
	private function _setName($name) {
		$this->name = $name;
	}

	/**
	* returns the name property 
	*
	* @return string
	**/
	public function getName() {
		return $this->name;
	}
}

/**
* parent class to tiles, special cards... basically the items a person playing the game can use to guide things
**/
abstract class Card {
	/** @var string the type of card **/
	private $cardType = null;

	/** @var string the name of the card **/
	private $name = null;

	/**
	* returns the cardType property
	*
	* @return string
	**/
	public function getCardType() {
		return $this->cardType;
	}

	/**
	* gets the name property
	*
	* @return string
	**/
	public function getName() {
		return $this->name;
	}
}

/**
* parent class for all terrain cards 
**/
abstract class Terrain extends Card {
	/** @var string all children of this class have a 'Terrain' cardType **/
	private $cardType = 'Terrain';

	/** @var boolean indicating whether this terrain can be traversed (i.e. walked through) **/
	private $traversable = null;

	/** @var integer how expensive is this terrain to traverse **/
	private $movementCost = null;

	/** @var integer some terrain attracts the NPC more than others **/
	private $drawScore = null;

	/** 
	* @var integer terrain could potentially increase/decrease encounter chances.  
   * null for no change
	* 0 for NO ENCOUNTERS
	* positive or negative values applied to normal encounter chance
	**/
	private $encounterModifier = null;

	/** @var integer some terrain has varying effects on morale **/
	private $moraleModifier = null;

	/** @var array presence of terrain types here indicates that the particular card must be layered on top **/
	private $placementRestrictions = null;

	/**
	* gets the traversable property
	*
	* @return boolean
	**/
	public function getTraversable() {
		return $this->traversable;
	}

	/**
	* gets the movementCost property
	*
	* @return integer
	**/
	public function getMovementCost() {
		return $this->movementCost;
	}

	/**
	* returns the encounterModifier property
	*
	* @return null|integer
	**/
	public function getEncounterModifier() {
		return $this->encounterModifier;
	}

	/**
	* returns the drawScore property
	*
	* @return integer
	**/
	public function getDrawScore() {
		return $this->drawScore();
	}

	/**
	* gets the moraleModifier property
	*
	* @return integer
	**/
	public function getMoraleModifier() {
		return $this->moraleModifier;
	}

	/** 
	* returns the placementRestrictions property
	*
	* @return null|array
	**/
	public function getPlacementRestrictions() {
		return $this->placementRestrictions;
	}
}

class Terrain_Dirt extends Terrain {
	private $name = "Dirt";
}

class Terrain_Light_Forest extends Terrain {
	private $name = "Light Forest";
}

class Terrain_Heavy_Forest extends Terrain {
	private $name = "Heavy Forest";
}

class Terrain_Dark_Forest extends Terrain {
	private $name = "Dark Forest";
}

class Terrain_Plain extends Terrain {
	private $name = "Plain";
}

class Terrain_Desert extends Terrain {
	private $name = "Desert";
}

class Terrain_Hill extends Terrain {
	private $name = "Hill";
}

class Terrain_Mountain extends Terrain {
	private $name = "Mountain";
}

class Terrain_Swamp extends Terrain {
	private $name = "Swamp";
}

class Terrain_Pond extends Terrain {
	private $name = "Pond";
}

class Terrain_Crevasse extends Terrain {
	private $name = "Crevasse";
}

class Terrain_Path extends Terrain {
	private $name = "Path";
}

class Terrain_Bridge extends Terrain {
	private $name = "Bridge";
}

/**
* cards like terrain, but landing on them triggers special events
**/
abstract class SpecialCard extends Card {
	/** @var string all children have a Special cardType **/
	private $cardType = "Special";
}

class Special_Inn extends SpecialCard {
	private $name = "Inn";
}

class Special_Cave extends SpecialCard {
	private $name = "Cave";
}

class Special_Castle extends SpecialCard {
	private $name = "Castle";
}

class Special_Village extends SpecialCard {
	private $name = "Village";
}

/**
* a type of card that changes stats or characteristics of the NPC
**/
abstract class NPCModifier extends Card {
	/** @var string the general cardType for all children **/
	private $cardType = "NPC Modifier";
}

class NPCModifier_HealthPotion extends NPCModifier {
	private $name = "Health Potion";
}

class NPCModifier_MoraleBoost extends NPCModifier {
	private $name = "Morale Boost";
}

class NPCModifier_ReduceEncounters extends NPCModifier {
	private $name = "Reduce Encounters";
}

class NPCModifier_NoEncounters extends NPCModifier {
	private $name = "No Encounters";
}

/**
* player cards that make adjustments to the map itself
**/
abstract class MapModifier extends Card {
	/** @var string the general cardType for all children **/
	private $cardType = "Map Modifier";
}

class EncounterZone_Change extends MapModifier {
	private $name = "Encounter Zone Change";
}

class BonusTerrain extends MapModifier {
	private $name = "Bonus Terrain";
}

class SomethingShiny extends MapModifier {
	private $name = "Something Shiny";
}

/**
GraphPaperRPG is a type of endless runner.
You play the dungeon master in a sense... creating a world on the fly for the computer controlled adventurer to explore and move through.  The objective being to keep your adventurer alive as long as possible for maximum score.


There is a constant day/night cycle on the overland.  The adventurer(s) will want to camp and rest when night comes.  

The computer adventurer, hereafter referred to as NPC, wanders around on the overland.  As the DM it is your job to fill in the world as it is being explored.  A fog of war type system... where the edges of the world around the character in the direction he is moving needs to be filled in before the character can navigate that terrain.

A tetris like system, or some kind of randomly drawn terrain 'hand' is used by the DM to select tiles from and fill in the map.   Can't just place whatever you want anywhere... There will be a certain 'luck of the draw' to it.

On game start, random NPC character is generated.
Stats include - 
HP - 1d20 (life force for the character.  when its gone, he's dead!)
Endurance - How vigorous or tired the character is.  Depletes as the NPC does stuff.
Morale - How gung ho is the NPC on the adventure?   Make things too boring, or too difficult, and you'll lose him!
Intelligence - Modifier used to lend some 'smarts' to choosing where the NPC moves, what he does, etc.
Dexterity - How fast the character travels.  Higher dexterity also allows the character to traverse differing types of terrain without losing speed.
Strength - Used as a modifier during combat
Sight - Determines how far the character can see. 
Luck - Number used for chance based rolls
Constitution - How hardy the character is.  Higher constitution and he won't get slowed down by adverse weather, or lose interest during long boring stretches of the game you've set him up on!  Can travel farther without needing rest.
Gold - Won after battles or found in places.

NPC leaes his home village and heads in a random direction (he moves like dragon warrior, only 4 possible directions  n/s/e/w)

He will continue in that direction until made to do otherwise.  

Player always able to play a PATH tile, which helps guide the NPC.

Other tiles come in randomly as they are spent.

The map is covered in 'zones', which serve as the base for monster encounter chance, monster encounter level.  Type of terrain sitting on top of these basis modify it.
Waterways are already laid out.

Special tiles or chunks of tiles where it is possible to play offmap tiles.

Why would the NPC ever leave the path?

Weather systems.

In addition to the terrain tiles, character can collect SPECIAL tiles.

Inn - Guide the NPC into here and you can save the game.

STREAM -> | 1 | 2 | 3 | 4 | 5 | 6 | -> GONE
       
        BANK -> | 1 | 2 | 3 | <- BANK  

1) NPC moves 1 tile
2) Exposed fog of war blinks.
3) Player fills in the blink

Terrain
- Lt. Forest
- Heavy Forest
- Dark Forest
- Plain
- Desert
- Hill
- Mountain
- Swamp
- Pond
- Crevasse

* Path 
* Inn
* Bridge
* Cave
* Castle
* Village
* Health Potion
* Morale Boost
* Heal Poison
* Ward of Monsters
* Modify Zone
* Change Weather
* Treasure (place on a terrain so when NPC runs over, collects item)
* Quest NPC?  Do we want the NPC to be given quests?

In the dungeon, maybe a mini game plays out... sort of like dig dug.  You have to dig the player a path through that produces different outcomes.
