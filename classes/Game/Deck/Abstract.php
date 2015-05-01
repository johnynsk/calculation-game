<?php
abstract class Game_Deck_Abstract
{
	/**
	 * Данные стека
	 *
	 * @var array
	 */
	protected $_vars = [];


	/**
	 * Данные стека - одномерный массив
	 */
	protected $_keys = [];


	/**
	 * Максимальный номинал
	 */
	const MAXNOMINAL = 13;


	/**
	 * Имя колоды
	 *
	 * @var string
	 */
	protected $_name;


	/**
	 * Ожидаемый номинал
	 *
	 * @var bool
	 */
	protected $_required = false;


	/**
	 * Лимит отображаемых карт
	 */
	protected $_limitCards = 0;


	/**
	 * Отображать все карты
	 *
	 * @return $this
	 */
	public function showAll()
	{
		$this->_limitCards = 0;

		return $this;
	}

	
	/**
	 * Отображать только одну карту
	 * 
	 * @return $this
	 */
	public function showOne()
	{
		$this->_limitCards = 1;

		return $this;
	}


	/**
	 * Получить лимиты отображения
	 */
	public function getLimitCards()
	{
		return $this->_limitCards;
	}


	/**
	 * Записать данные в массив
	 *
	 * @param Game_Card $card
	 */
	function push(Game_Card $card)
	{
		$this->_vars[] = $card;
		$this->_keys[$card->suit . '_' . $card->nominal] = 1;
		return true;
	}


	/**
	 * Выдернуть последний элемент массива
	 *
	 * @return Game_Card
	 */
	function pop()
	{
		$card = array_pop($this->_vars);
		if ($card) {
			unset($this->_keys[$card->suit . '_' . $card->nominal]);
		}

		return $card;
	}


	/**
	 * Вытащить первый элемент массива
	 * 
	 * @return Game_Card
	 */
	function shift()
	{
		$card = array_shift($this->_vars);
		unset($this->_keys[$card->suit . '_' . $card->nominal]);

		return $card;
	}


	/**
	 * Получить верхний элемент массива
	 *
	 * @return Game_Card
	 */
	function getPop()
	{
		return end($this->_vars);
	}

	
	/**
	 * Получить верхний элемент массива
	 *
	 * @return Game_Card
	 */
	function getShift()
	{
		return reset($this->_vars);
	}


	/**
	 * Получить все ключи
	 *
	 * @return string[]
	 */
	function getKeys()
	{
		return $this->_keys;
	}


	/**
	 * Получить количество карт
	 * 
	 * @return int
	 */
	function getCount()
	{
		return count($this->_vars);
	}


	/**
	 * Получить все карты
	 *
	 * @return Game_Card[]
	 */
	function getCards()
	{
		return $this->_vars;
	}


	/**
	 * Поиск карты в колоде
	 * 
	 * @param int $suit
	 * @param int $nominal
	 */
	function search($suit, $nominal)
	{
		if (!array_key_exists($suit . '_'. $nominal, $this->_keys)) {
			return false;
		}
		return true;
	}


	/**
	 * Установить имя колоды
	 *
	 * @param string $name
	 */
	function setName($name)
	{
		$this->_name = $name;
	}


	/**
	 * Получить имя колоды
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->_name;
	}


	/**
	 * Получить требуемый номинал
	 *
	 * @return int
	 */
	public function getRequired()
	{
		return $this->_required;
	}
}
