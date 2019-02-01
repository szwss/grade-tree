<?php

namespace Szwss\GradeTree;

use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * NodeCategoryTrait
 *
 * @property integer $id
 * @property integer $parent_id
 * @property boolean $level
 * @property string $name
 * @property string $node
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @package VergilLai\NodeCategories
 * @author Vergil <vergil@vip.163.com>
 */
trait NodeCategoryTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function getParent()
    {
        return $this->findOrFail($this->parent_id);
    }

    /**
     * Get all children node
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function childrens()
    {
        return $this->where('node', 'like', $this->node.'%')->where('id', '<>', $this->id)->get();
    }

    /**
     * Get all parent node
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function parents()
    {
        return $this->where(DB::raw("LOCATE(`node`, '{$this->node}')"), '>', 0)
            ->where('id', '<>', $this->id)->get();
    }

    /**
     * Get all category name
     *
     * @param string $delimiter
     * @return string
     */
    public function fullPathName($delimiter = '/')
    {
        $parents = $this->parents();

        return implode($delimiter, $parents->pluck('name')->toArray()) . $delimiter . $this->name;
    }

    /**
     * Get tree structures
     * @param callable $map
     * @return array
     */
    public static function getTree(\Closure $map = null)
    {
        return static::makeTree((new static)->get(), $map);
    }

    /**
     * Get tree structures
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param callable $map
     * @return array
     */
    public static function makeTree(\Illuminate\Database\Eloquent\Collection $collection, \Closure $map = null)
    {
        if (is_callable($map)) {
            $collection->map($map);
        }

        $categories = $collection->keyBy('id')->toArray();

        $nodesKey = (new static)->getNodesKey();

        foreach($categories as $cate) {
            $categories[$cate['parent_id']][$nodesKey][] = & $categories[$cate['id']];
        }

        return isset($categories[0][$nodesKey]) ? $categories[0][$nodesKey] : [];
    }

    /**
     * Get the tree structures nodes key name.
     * @return string
     */
    protected function getNodesKey()
    {
        return property_exists($this, 'nodesKey') ? $this->nodesKey : 'nodes';
    }
}
