<?php

require_once("Database.php");

class Crud {

	/**
	 * @var PDO
	 */
	private $db;

	/**
	 * @var string
	 */
	private string $table;

	/**
	 * Stores the database connection and the table we are using
	 *
	 * @param Database $db Database object containing the PDO connection
	 * @param string $table Database table we are going to access
	 */
	public function __construct(Database $db, string $table){
		$this->db    = $db;
		$this->table = $table;
	}

	/**
	 * Creates a new record in the database table
	 *
	 * @param array<string,string|int|float|boolean> $data Data to insert formatted as array(columnName => value)
	 *
	 * @return integer
	 */
	public function Create(array $data): int{
		$sql = $this->db->prepareInsert($this->table, $data);

		if (false === $sql->execute()){
			throw new \RuntimeException($sql->errorInfo(), $sql->errorCode());
		}

		return $this->db->lastInsertId();
	}

	/**
	 * Read rows from the table
	 *
	 * @param array $columns A list of columns to fetch
	 * @param array|string $where   Multi-dimensional array or a SQL string for complex restrictions
	 *                                Refer to the readme for examples
	 * @param integer $limit   How many rows to fetch
	 *
	 * @return array
	 */
	public function Read(array $columns = ['*'], array|string $where = [], int $limit = 0): array{
		$sql = $this->db->prepareRead($this->table, $columns, $where);

		if (false === $sql->execute()){
			throw new \RuntimeException($this->db->errorInfo(), $this->db->errorCode());
		}

		return $sql->fetchAll();
	}

	/**
	 * Updates specified columns in the table
	 *
	 * @param array $data  The data that is being updated, formatted as array(columnName => newValue)
	 * @param array|string $where Multi-dimensional array or a SQL string for complex restrictions
	 *                             Refer to the readme for examples
	 *
	 * @return integer
	 */
	public function Update(array $data, array|string $where): int{
		$sql = $this->db->prepareUpdate($this->table, $data, $where);

		if (false === $sql->execute()){
			throw new \RuntimeException($sql->errorInfo(), $sql->errorCode());
		}

		return $sql->rowCount();
	}

	/**
	 * Deletes rows from the table
	 *
	 * @param array|string $where Multi-dimensional array or a SQL string for complex restrictions
	 *                              Refer to the readme for examples
	 *
	 * @return boolean
	 */
	public function Delete(array|string $where = []): bool{
		$sql = $this->db->prepareDelete($this->table, $where);

		if (false === $sql->execute()){
			throw new \RuntimeException($this->db->errorInfo(), $this->db->errorCode());
		}

		return true;
	}

	/**
	 * Manually execute a SQL query
	 *
	 * @param string $query [description]
	 * @return array
	 */
	public function Execute(string $query): array{
		$sql = $this->db->query($query);

		if (false === $sql || false === $sql->execute()){
			throw new \RuntimeException($this->db->errorInfo(), $this->db->errorCode());
		}

		return $sql->fetchAll();
	}
}

