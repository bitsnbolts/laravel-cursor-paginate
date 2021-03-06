<?php

namespace Bitsnbolts\CursorPaginate;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Collection;

class CursorPaginator implements Arrayable, Jsonable
{
    protected Collection $items;
    protected int $total;
    protected $nextCursor;
    protected $currentCursor;
    protected array $params = [];

    public function __construct($items, $total, $nextCursor = null)
    {
        $this->items = $items;
        $this->total = $total;
        $this->nextCursor = $nextCursor;
        $this->currentCursor = self::currentCursor();
    }

    public static function currentCursor()
    {
        return json_decode(base64_decode(request('cursor')));
    }

    /**
     * @return static
     */
    public function appends($params): self
    {
        $this->params = $params;

        return $this;
    }

    public function items(): Collection
    {
        return $this->items;
    }

    public function count(): int
    {
        return $this->items->count();
    }

    public function total(): int
    {
        return $this->total;
    }

    public function currentCursorUrl(): string
    {
        if ($this->currentCursor) {
            return url()->current().'?'.http_build_query(array_merge([
                'cursor' => base64_encode(json_encode($this->currentCursor)),
            ], $this->params));
        }

        $result = url()->current();
        if (count($this->params) > 0) {
            $result .= '?'.http_build_query($this->params);
        }

        return $result;
    }

    public function nextCursorUrl(): ?string
    {
        return $this->nextCursor ? url()->current().'?'.http_build_query(array_merge([
            'cursor' => base64_encode(json_encode($this->nextCursor)),
        ], $this->params)) : null;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'self' => $this->currentCursorUrl(),
            'next' => $this->nextCursorUrl(),
            'count' => $this->count(),
            'total' => $this->total(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }
}
