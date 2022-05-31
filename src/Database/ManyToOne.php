<?php

namespace Equit\Database;

use PDO;

/**
 * A model relation that links many local models to a single related model.
 *
 * This is often a "belongs-to" relation.
 */
class ManyToOne extends Relation
{
    /** @var Model|null The related model. */
    protected ?Model $relatedModel;

    /**
     * @var bool Whether the related model has been fetched from the db.
     *
     * Since the model in the relation can be null, we need a flag to avoid querying the db every time the model is
     * requested from the relation if the related model is legitimately null.
     */
    private bool $fetched = false;

    /**
     * @inheritDoc
     */
    public function reload(): void
    {
        $relatedClass = $this->relatedModel();
        $stmt = $this->localModel()->connection()->prepare("SELECT * FROM `" . $relatedClass::table() . "` WHERE `{$this->relatedKey()}` = ? LIMIT 1");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute([$this->localModel()->{$this->localKey()}]);
        $this->relatedModel = $this->makeModelsFromQuery($stmt)[0] ?? null;
        $this->fetched = true;
    }

    /**
     * @inheritDoc
     */
    public function relatedModels(): ?Model
    {
        if (!$this->fetched) {
            $this->reload();
        }

        return $this->relatedModel;
    }
}
