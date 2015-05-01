<?php
class Game_Deck_Holder extends Game_Deck_Abstract
{
	/**
	 * Шаг хранилища карт
	 *
	 * @var int
	 */
	protected $_step = 1;


	/**
	 * Записать данные в массив
	 *
	 * @param Game_Card $card
	 */
	public function push($card)
	{
		$card->dump();
		if ($card->nominal != $this->_required) {
			trigger_error('Попытка добавить карту не в ту колоду ' . $card->nominalIcon . $card->suitIcon .' ? ' . $this->_step);
			return false;
		}

		parent::push($card);

		$this->_required += $this->_step;

		if ($this->_required >= self::MAXNOMINAL) {
			$this->_required %= self::MAXNOMINAL;
		}

		if (count($this->_vars) >= self::MAXNOMINAL) {
			$this->_required = false;
		}

		return true;
	}

	
	/**
	 * @param int $step
	 * @param array $card
	 */
	function __construct($step, $suit, $nominal)
	{
		$card = new Game_Card($suit, $nominal);
		$this->_required = $nominal;
		$this->_step = $step;

		$this->push($card);
	}
}
