<?php /** MicroGridViewWidget */

namespace Micro\Widget;

use Micro\Base\Exception;
use Micro\Db\ConnectionInjector;
use Micro\File\Type;
use Micro\Mvc\Models\IModel;
use Micro\Mvc\Models\IQuery;
use Micro\Mvc\Models\Query;
use Micro\Mvc\Widget;
use Micro\Web\Html\Html;
use Micro\Web\RequestInjector;

/**
 * GridViewWidget class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Widget
 * @version 1.0
 * @since 1.0
 */
class GridViewWidget extends Widget
{
    /** @var int $page Current page on table */
    public $page = 0;
    /** @var int $limit Limit current rows */
    public $limit = 10;
    /** @var bool $filters Usage filters */
    public $filters = true;
    /** @var string $template Template render */
    public $template = '{counter}{table}{pager}';
    /** @var string $templateTable Template table render */
    public $templateTable = '{headers}{filters}{rows}';
    /** @var string $textCounter text for before counter */
    public $counterText = 'Sum: ';
    /** @var string $emptyText text to render if rows not found */
    public $emptyText = 'Elements not found';
    /** @var array $attributesEmpty Attributes for empty text */
    public $attributesEmpty = [];
    /** @var array $attributes attributes for table */
    public $attributes = [];
    /** @var array $attributesCounter attributes for counter */
    public $attributesCounter = [];
    /** @var array $attributesHeading attributes for heading */
    public $attributesHeading = [];
    /** @var array $attributesFilter attributes for filter row */
    public $attributesFilter = [];
    /** @var array $attributesFilterForm attributes for filter form */
    public $attributesFilterForm = [];
    /** @var array $tableConfig table configuration */
    public $tableConfig = [];
    /** @var array $paginationConfig parameters for PaginationWidget */
    public $paginationConfig = [];

    /** @var array $rows Rows from data */
    protected $rows;
    /** @var array $fields Fields of data */
    protected $fields = [];
    /** @var int $rowsCount Count rows */
    protected $rowsCount = 0;
    /** @var int $totalCount Total count data */
    protected $totalCount = 0;
    /** @var string $filterPrefix prefix for filter name */
    protected $filterPrefix;


    /**
     * Re-declare widget constructor
     *
     * @access public
     *
     * @param array $args arguments
     *
     * @result void
     * @throws Exception
     */
    public function __construct(array $args = [])
    {
        parent::__construct($args);

        if (empty($args['data'])) {
            throw new Exception('Argument "data" not initialized into GridViewWidget');
        }

        $this->limit = ($this->limit < 10) ? 10 : $this->limit;
        $this->page = ($this->page < 0) ? 0 : $this->page;

        $data = $args['data'];

        if ($data instanceof IQuery) {
            if ($data->objectName) {
                /** @var IModel $cls */
                $cls = $data->objectName;
                /** @noinspection PhpUndefinedFieldInspection */
                $data->table = $cls::$tableName;
            } elseif (!$args['data']->table) {
                throw new Exception('Data query not set table or objectName');
            }

            if ($data->having || $data->group) {
                $res = new Query((new ConnectionInjector)->get());
                $res->select = 'COUNT(*)';
                $res->table = '('.$data->getQuery().') micro_count';
                $res->single = true;
            } else {
                /** @var Query $res */
                $res = clone $data;
                $res->objectName = null;
                $res->select = 'COUNT(*)';
                $res->single = true;
            }

            /** @var array $a */
            $this->totalCount = ($a = $res->run()) ? $a[0] : 0;
            $this->filterPrefix = $data->table;

            $data->ofset = $this->page * $this->limit;
            $data->limit = $this->limit;
            $data = $data->run($data->objectName ? \PDO::FETCH_CLASS : \PDO::FETCH_ASSOC);
        } else { // array
            $this->totalCount = count($data);
            $data = array_slice($data, $this->page * $this->limit, $this->limit);
        }

        foreach ($data AS $model) {
            $this->rows[] = is_subclass_of($model, 'Micro\Mvc\Models\Model') ? $model : (object)$model;
        }
    }

    /**
     * Initialize widget
     *
     * @access public
     *
     * @result void
     */
    public function init()
    {
        $this->filterPrefix = ucfirst($this->filterPrefix ?: 'data'.$this->totalCount);
        $this->fields = (null !== $this->rows) ? array_keys(Type::getVars($this->rows[0])) : [];
        $this->rowsCount = count($this->rows);
        $this->paginationConfig['countRows'] = $this->totalCount;
        $this->paginationConfig['limit'] = $this->limit;
        $this->paginationConfig['currentPage'] = $this->page;
        $this->tableConfig = $this->tableConfig ?: $this->fields;

        foreach ($this->tableConfig AS $key => $conf) {
            unset($this->tableConfig[$key]);

            $this->tableConfig[is_string($conf) ? $conf : $key] = array_merge([
                'attributesHeader' => !empty($conf['attributesHeader']) ? $conf['attributesHeader'] : [],
                'attributesFilter' => !empty($conf['attributesFilter']) ? $conf['attributesFilter'] : [],
                'attributes' => !empty($conf['attributes']) ? $conf['attributes'] : []
            ], is_array($conf) ? $conf : []);
        }
    }

    /**
     * Running widget
     *
     * @access public
     *
     * @return string
     */
    public function run()
    {
        if (!$this->rows) {
            return Html::openTag('div', $this->attributesEmpty).$this->emptyText.Html::closeTag('div');
        }

        ob_start();
        echo str_replace(
            ['{counter}', '{pager}', '{table}'],
            [$this->getCounter(), $this->getPager(), $this->getTable()],
            $this->template
        );

        return ob_get_clean();
    }

    /**
     * Get counter
     *
     * @access protected
     *
     * @return string
     */
    protected function getCounter()
    {
        return Html::openTag('div', $this->attributesCounter).
        $this->counterText.$this->totalCount.Html::closeTag('div');
    }

    /**
     * Get pager
     *
     * @access protected
     *
     * @return string
     */
    protected function getPager()
    {
        if (!$this->rows) {
            return '';
        }

        $pager = new PaginationWidget($this->paginationConfig);
        $pager->init();

        return $pager->run();
    }

    /**
     * Get table
     *
     * @access protected
     *
     * @return string
     */
    protected function getTable()
    {
        $table = str_replace(
            ['{headers}', '{filters}', '{rows}'],
            [$this->renderHeading(), $this->renderFilters(), $this->renderRows()],
            $this->templateTable
        );

        return Html::openTag('table', $this->attributes).$table.Html::closeTag('table');
    }

    /**
     * Render heading
     *
     * @access protected
     *
     * @return string
     */
    protected function renderHeading()
    {
        $result = Html::openTag('tr', $this->attributesHeading);
        foreach ($this->tableConfig AS $key => $row) {
            $result .= Html::openTag('th', $row['attributesHeader']);
            if (!empty($row['header'])) {
                $result .= $row['header'];
            } else {
                if (is_string($key)) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $result .= is_subclass_of($this->rows[0],
                        'Micro\\Mvc\\Models\\Model') ? $this->rows[0]->getLabel($key) : ucfirst($key);
                }
            }
            $result .= Html::closeTag('th');
        }

        return $result.Html::closeTag('tr');
    }

    /**
     * Render filters
     *
     * @access protected
     *
     * @return null|string
     * @throws Exception
     */
    protected function renderFilters()
    {
        if (!$this->filters) {
            return null;
        }
        /** @var array $filtersData */
        $filtersData = (new RequestInjector)->get()->query($this->filterPrefix);

        $result = Html::beginForm(null, 'get', $this->attributesFilterForm);
        $result .= Html::openTag('tr', $this->attributesFilter);

        foreach ($this->tableConfig AS $key => $row) {
            $result .= Html::openTag('td', $row['attributesFilter']);
            if (array_key_exists('filter', $row) && $row['filter'] === false) {
                continue;
            }
            if (!empty($row['filter'])) {
                $result .= $row['filter'];
            } else {
                $buffer = is_array($row) ? $key : $row;
                $fieldName = $this->filterPrefix.'['.$buffer.']';
                $fieldId = $this->filterPrefix.'_'.$buffer;
                $val = !empty($filtersData[$buffer]) ? $filtersData[$buffer] : '';
                $result .= Html::textField($fieldName, $val, ['id' => $fieldId]);
            }
            $result .= Html::closeTag('td');
        }

        return $result.Html::closeTag('tr').Html::endForm();
    }

    /**
     * Render rows
     *
     * @access protected
     *
     * @return null|string
     */
    protected function renderRows()
    {
        $result = null;

        if (0 === count($this->rows)) {
            return Html::openTag('tr').
            Html::openTag('td', ['cols' => count($this->fields)]).$this->emptyText.Html::closeTag('td').
            Html::closeTag('tr');
        }

        foreach ($this->rows AS $data) {
            $result .= Html::openTag('tr');

            foreach ($this->tableConfig AS $key => $row) {
                $result .= Html::openTag('td', $row['attributes']);

                if (!empty($row['class']) && is_subclass_of($row['class'], 'Micro\\Widget\\GridColumn')) {
                    $primaryKey = $data->{!empty($row['key']) ? $row['key'] : 'id'};
                    $result .= (string)(new $row['class'](
                        $row + ['str' => (null === $data) ?: $data, 'pKey' => $primaryKey]
                    ));
                } elseif (!empty($row['value'])) {
                    $result .= eval('return '.$row['value'].';');
                } else {
                    $result .= property_exists($data, $key) ? $data->$key : null;
                }
                $result .= Html::closeTag('td');
            }
            $result .= Html::closeTag('tr');
        }

        return $result;
    }
}
