<?php

namespace Muswalo\Surgemusic\Models\Base;

use Muswalo\Surgemusic\Models\Base\Interfaces\ModelInterface;
use Muswalo\Surgemusic\Models\Base\DatabaseConnection;
use Muswalo\Surgemusic\Models\Base\Interfaces\SoftDeleteInterface;
use Muswalo\Surgemusic\Models\Base\Interfaces\TimestampInterface;

use PDO;
use Exception;
/**
 * Abstract base model class providing common database interaction functionality.
 */
abstract class Model implements ModelInterface, TimestampInterface, SoftDeleteInterface
{
    /**
     *  @var PDO Database connection instance. 
     * 
     */
    protected PDO $db;

    /** 
     * 
     * @var string Database table name associated with the model. 
     * 
     */
    protected string $table;

    /** 
     * 
     * @var string Primary key column name. 
     * 
     */
    protected string $primaryKey = 'id';

    /**
     *  @var array List of fillable attributes for the model. 
     * 
     */

    protected array $fillable = [];

    /** 
     * 
     * @var array Attribute values for the model instance. 
     * 
     */
    protected array $attributes = [];

    /** 
     * @var array Original attribute values for change tracking. 
     * 
     */
    protected array $original = [];

    /** 
     * 
     * @var array Relations to be eager-loaded with the model. 
     * 
     */
    protected array $relations = [];

    /** 
     * 
     * @var array Query scopes applied to the model. 
     * 
     */
    protected array $scopes = [];

    /**
     * 
     *  @var bool Whether to manage created_at and updated_at timestamps. 
     * 
     */
    protected bool $timestamps = true;

    /**
     * 
     *  @var string Column name for the created_at timestamp.
     */
    protected string $createdAtColumn = 'created_at';

    /**
     *  @var string Column name for the updated_at timestamp. 
     * 
     */
    protected string $updatedAtColumn = 'updated_at';

    /** 
     * 
     * @var array Parameters for query scopes. 
     
     */
    protected array $scopesParams = [];

    // Abstract methods to be implemented by subclasses
    protected function beforeSave(): void {}
    protected function afterSave(): void {}
    protected function beforeDelete(): void {}
    protected function afterDelete(): void {}

    /**
     * 
     * Constructor initializes the database connection.
     * 
     */
    public function __construct()
    {
        $this->db = DatabaseConnection::connect();
    }

    /**
     * Retrieve paginated records from the table.
     *
     * @param int $perPage Number of records per page.
     * @param int $page Current page number.
     * @return array An array containing data and pagination metadata.
     * 
     */
    public function getAllPaginated(int $perPage = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table} {$this->buildScopes()} LIMIT :perPage OFFSET :offset ;";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
            ],
        ];
    }

    /**
     * Find a record by its primary key.
     *
     * @param int $id Primary key value.
     * @return self|null The model instance or null if not found.
     * 
     */
    public function find(int $id): ?self
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id {$this->buildScopes()};"; 
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);        
        $stmt->execute();    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $this->fill($result);
            return $this;
        }
        return null;
    }
    
    /**
     * Find a record by a specific column value.
     *
     * @param string $column Column name.
     * @param mixed $value Column value.
     * @return self|null The model instance or null if not found.
     * 
     */

    public function findBy(string $column, mixed $value): ?self
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? " . $this->buildScopes());
        $stmt->execute([$value]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->fill($result);
            return $this;
        }

        return null;
    }

    /**
     * Create a new record in the database.
     *
     * @param array $data Data to be inserted.
     * @return self The model instance.
     * 
     */

    public function create(array $data): self
    {
        $this->fill($data);
        $this->beforeSave();

        if ($this->timestamps) {
            $this->attributes[$this->createdAtColumn] = $this->attributes[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }

        $fillableData = array_intersect_key($this->attributes, array_flip($this->fillable));
        $columns = implode(', ', array_keys($fillableData));
        $placeholders = implode(', ', array_fill(0, count($fillableData), '?'));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($fillableData));

        $this->attributes[$this->primaryKey] = $this->db->lastInsertId();
        $this->afterSave();

        return $this;
    }

    /**
     * Update an existing record in the database.
     *
     * @param array $data Data to be updated.
     * @return bool True if the update was successful, false otherwise.
     * 
     */
    public function update(int $id, array $data): bool
    {
        $invalidColumns = array_diff(array_keys($data), $this->fillable);
        if (!empty($invalidColumns)) {
            throw new Exception('Invalid columns provided: ' . implode(', ', $invalidColumns));
        }

        $this->fill($data);
        $this->beforeSave();
    
        if ($this->timestamps) {
            $this->attributes[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }
    
        $fillableData = array_intersect_key($this->attributes, array_flip($this->fillable));
        $updates = [];
    
        foreach ($fillableData as $key => $value) {
            $updates[] = "{$key} = :{$key}";
        }
    
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
    
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    
        foreach ($fillableData as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
    
        $result = $stmt->execute();
        $this->afterSave();

        return $result;
    }
    
    /**
     * Delete a record from the database.
     *
     * @return bool True if the deletion was successful, false otherwise.
     * 
     */
    public function delete(int $id): bool
    {
        $this->beforeDelete();
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        $this->afterDelete();
        return $result;
    }

    /**
     * Insert multiple records into the database.
     * 
     * @param array $rows Array of rows to be inserted.
     * @return bool True if the insertion was successful, false otherwise.
     * 
     */
    public function bulkInsert(array $rows): bool
    {
        if (empty($rows)) {
            return false;
        }

        $columns = implode(', ', array_keys($rows[0]));
        $placeholders = '(' . implode(', ', array_fill(0, count($rows[0]), '?')) . ')';
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES " .
            implode(', ', array_fill(0, count($rows), $placeholders));

        $values = [];
        foreach ($rows as $row) {
            $values = array_merge($values, array_values($row));
        }

        $stmt = $this->db->prepare($query);
        return $stmt->execute($values);
    }

    /**
     * Add a WHERE clause to the query.
     * @param string $column Column name.
     * @param string $operator Comparison operator.
     * @param mixed $value Value to compare against.
     * @return self The model instance.
     */
    public function where(string $column, string $operator, mixed $value): self
    {
        $this->scopes[] = "{$column} {$operator} ?";

        $this->scopesParams[] = $value;
        return $this;
    }

    /**
     * Add an ORDER BY clause to the query.
     * @param string $column Column name.
     * @param string $direction Sort direction.
     * @return self The model instance.
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->scopes[] = "ORDER BY {$column} {$direction}";
        return $this;
    }

    /**
     * Add a LIMIT clause to the query.
     * @param int $limit Maximum number of records to return.
     * @return self The model instance.
     */
    public function limit(int $limit): self
    {
        $this->scopes[] = "LIMIT {$limit}";
        return $this;
    }

    /**
     * Build sql query scopes.
     * @return string The sql query scopes.
     * 
     */
    protected function buildScopes(): string
    {
        $sql = implode(' ', $this->scopes);
        if (!empty($this->scopesParams)) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE " . $sql);
            $stmt->execute($this->scopesParams);
        }
        return $sql;
    }

    /**
     * 
     * Retrieve paginated records from the table.
     * @param int $perPage Number of records per page.
     * @param int $page Current page number.
     * @return array An array containing data and pagination metadata.
     * 
     */
    public function paginate(int $perPage, int $page): array
    {
        $offset = ($page - 1) * $perPage;
        $this->limit($perPage)->where("1", "=", 1);
        $stmt = $this->db->query("SELECT * FROM {$this->table} {$this->buildScopes()} OFFSET {$offset}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Eager load relations with the model.
     * @param string $relation Relation name.
     * @return self The model instance.
     * 
     */
    public function with(string $relation): self
    {
        $this->relations[] = $relation;
        return $this;
    }

    /**
     * Fill the model with an array of attributes.
     * @param array $attributes Array of attributes to fill.
     * @return self The model instance.
     * 
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        $this->original = $this->attributes;
        return $this;
    }

    /**
     * Get unmodified original attribute values.
     * @return array The original attribute values.
     * 
     */
    public function getDirty(): array
    {
        return array_diff_assoc($this->attributes, $this->original);
    }

    /**
     * Convert the model instance to an array.
     * @return array The model attributes.
     * 
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Updates or create a record in the database.
     * @return bool True if the operation was successful, false otherwise.
     * 
     */
    public function save(?int $id = null): bool
    {
        return isset($this->attributes[$this->primaryKey]) ?
            $this->update($id, $this->attributes) :
            $this->create($this->attributes) !== null;
    }

    // *********************** CRUD METHODS ************************

    /**
     * Set a specific attribute value.
     * @param string $key Attribute name.
     * @param mixed $value Attribute value.
     */
    public function setAttritube(string $key, $value): void
    {
        $this->attributes[$key] = $value;
        $this->save();
    }

    /**
     * Get a specific attribute value.
     * @param string $key Attribute name.
     * @return string|null The attribute value or null if not found.
     */
    public function getAttribute(string $key): ?string
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Get the ID of a record
     * @return int The primary key value.
     */
    public function getId(): int
    {
        return $this->primaryKey;
    }

    /**
     * Get all attributes of the model
     * @return array The model attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    //  ********************* TimestampInterface methods **********************  

    /**
     * Get the created_at timestamp.
     * @return string The created_at timestamp.
     */
    public function getCreatedAt(): string 
    {
        return $this->attributes['created_at'] ?? '';
    }

    /**
     * Get the updated_at timestamp.
     * @return string The updated_at timestamp.
     */
    public function getUpdatedAt(): string 
    {
        return $this->attributes['updated_at'] ?? '';
    }

    /**
     * Set the updated_at timestamp.
     */
    public function setUpdatedAt(): void
    {
        $this->attributes['updated_at'] = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Touch the updated_at timestamp.
     * @return void
     */
    public function touchTimestamp(): void 
    {
        $this->setUpdatedAt();
        $this->save();
    }

    // ************************ SoftDeleteInterface methods ************************
    
    /**
     * Soft delete a record.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function softDelete(): bool 
    {
        $this->attributes['deleted_at'] = date('Y-m-d H:i:s');
        return $this->save();
    }
    
    /**
     * Restore a soft-deleted record.
     * @return bool True if the restoration was successful, false otherwise.
     */
    public function restore(): bool 
    {
        $this->attributes['deleted_at'] = null;
        return $this->save();
    }
    
    /**
     * Permanently delete a record.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function forceDelete(int $id): bool 
    {
        return $this->delete($id);
    }
    
    /**
     * Check if a record has been soft-deleted.
     * @return bool True if the record has been soft-deleted, false otherwise.
     */
    public function isSoftDeleted(): bool 
    {
        return !empty($this->attributes['deleted_at']);
    }

}
