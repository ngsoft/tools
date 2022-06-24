<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use InvalidArgumentException,
    NGSOFT\Tools,
    PDO,
    PDOStatement,
    Stringable,
    Throwable;
use function str_starts_with;

/**
 * A SQLite database to manage your locks
 */
class SQLiteLock extends BaseLockStore
{

    protected const COLUMN_NAME = 'name';
    protected const COLUMN_OWNER = 'owner';
    protected const COLUMN_UNTIL = 'until';

    protected PDO $driver;

    /**
     * @param string|Stringable $name Lock name
     * @param int|float $seconds lock duration
     * @param string|PDO $database db3 filename or pdo instance
     * @param string|Stringable $owner the owner of the lock
     * @param bool $autoRelease release lock when object is destroyed
     * @param string $table table name to tuse for the locks
     * @throws InvalidArgumentException if invalid pdo driver
     */
    public function __construct(
            string|Stringable $name,
            int|float $seconds = 0,
            string|PDO $database = '',
            string|Stringable $owner = '',
            bool $autoRelease = true,
            protected string $table = 'locks'
    )
    {
        parent::__construct($name, $seconds, $owner, $autoRelease);

        if (empty($database)) {
            $database = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sqlock.db3';
        }

        if (is_string($database)) {
            $database = new PDO(sprintf('sqlite:%s', $database));
        }

        $type = $database->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($type !== 'sqlite') {
            throw new InvalidArgumentException(sprintf('Invalid PDO driver, sqlite requested, %s given.', $type));
        }
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->driver = $database;

        $this->createTable($table);
    }

    protected function getColumns(): array
    {

        return [
            self::COLUMN_NAME,
            self::COLUMN_OWNER,
            self::COLUMN_UNTIL,
        ];
    }

    protected function createTable(string $table)
    {

        $query = sprintf(
                'CREATE TABLE IF NOT EXISTS %s(%s TEXT PRIMARY KEY NOT NULL, %s TEXT, %s REAL)',
                $table,
                static::COLUMN_NAME,
                static::COLUMN_OWNER,
                static::COLUMN_UNTIL,
        );

        $this->driver->exec($query);
    }

    protected function prepare(string $query, array $bindings = []): PDOStatement|false
    {
        try {
            Tools::errors_as_exceptions();
            $prepared = $this->driver->prepare($query);
            foreach ($bindings as $index => $value) {
                if (is_string($index) && ! str_starts_with($index, ':')) {
                    $index = ":$index";
                }
                if (is_int($index)) $index ++;
                $prepared->bindValue($index, $value);
            }

            return $prepared;
        } catch (Throwable $err) {
            return false;
        } finally { \restore_error_handler(); }
    }

    protected function purge(): void
    {

        try {
            Tools::errors_as_exceptions();

            $statement = $this->prepare(
                    sprintf(
                            'DELETE FROM %s WHERE %s < ?',
                            $this->table,
                            self::COLUMN_UNTIL
                    ), [$this->timestamp()]);

            $statement && $statement->execute();
        } catch (Throwable) {

        } finally { \restore_error_handler(); }
    }

    protected function read(): array|false
    {

        $this->purge();
        if (
                $statement = $this->prepare(sprintf(
                        'SELECT %s FROM %s WHERE %s = ? LIMIT 1',
                        implode(',', $this->getColumns()),
                        $this->table,
                        self::COLUMN_NAME
                ), [$this->getHashedName()])
        ) {

            if ($statement->execute()) {
                if ($arr = $statement->fetch(PDO::FETCH_ASSOC)) {
                    return [
                        self::KEY_UNTIL => $arr[self::COLUMN_UNTIL],
                        self::KEY_OWNER => $arr[self::COLUMN_OWNER]
                    ];
                }
            }
        }

        return false;
    }

    protected function write(int|float $until): bool
    {
        if ($statement = $this->prepare(sprintf(
                        'INSERT OR REPLACE INTO %s (%s) VALUES (?, ?, ?)',
                        $this->table, implode(',', $this->getColumns())
                ), [$this->getHashedName(), $this->getOwner(), $until])
        ) {

            if ($statement->execute()) {
                $this->until = $until;
                return true;
            }
        }

        return false;
    }

    /** {@inheritdoc} */
    public function forceRelease(): void
    {
        if (
                $statement = $this->prepare(sprintf(
                        'DELETE FROM %s WHERE %s = ?',
                        $this->table, self::COLUMN_NAME
                ), [$this->getHashedName()])
        ) {
            if ($statement->execute()) {
                $this->until = 1;
            }
        }
    }

}
