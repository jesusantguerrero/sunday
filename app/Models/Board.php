<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;
    protected $with = ['labels', 'fields'];
    protected $fillable = ['name', 'user_id', 'team_id', 'workspace_id', 'board_template_id', 'board_type_id', 'color'];

    public function stages() {
        return $this->hasMany('App\Models\Stage', 'board_id', 'id')->orderBy('order');
    }

    public function fields() {
        return $this->hasMany('App\Models\Field', 'board_id', 'id');
    }

    public function labels() {
        return $this->hasMany('App\Models\Label', 'board_id', 'id');
    }

    public function boardTemplate() {
        return $this->belongsTo('App\Models\BoardTemplate');
    }

    public function boardType() {
        return $this->belongsTo('App\Models\BoardType');
    }

    public function createMainStage() {
        $this->stages()->create([
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'name' => $this->name
        ]);

        $this->setUp();
    }

    public function setUp() {
        $defaultTemplate = $this->boardTemplate ?? BoardTemplate::find(1);
        $templateConfig = json_decode($defaultTemplate->config, true);
        $fields = $templateConfig['fields'];
        $stages = $templateConfig['stages'] ?? null;

        if (count($fields)) {
            foreach ($fields as $field) {
                $fieldDB = $this->fields()->create([
                    'user_id' => $this->user_id,
                    'team_id' => $this->team_id,
                    'name' => $field['name'],
                    'title' => $field['title'],
                    'type' => $field['type'],
                ]);

                if (isset($field['labels'])) {
                    foreach ($field['labels'] as $label) {
                        $fieldDB->labels()->create([
                            'user_id' => $fieldDB->user_id,
                            'team_id' => $fieldDB->team_id,
                            'board_id' => $fieldDB->board_id,
                            'name' => $label['name'],
                            'label' => $label['label'],
                            'color' => $label['color'],
                        ]);
                    }

                    $fieldDB->rules()->create([
                        'user_id' => $fieldDB->user_id,
                        'team_id' => $fieldDB->team_id,
                        'board_id' => $fieldDB->board_id,
                        'name'=> 'bg',
                        'reference' => 'options'
                    ]);
                }
            }
        }

        if ($stages) {
            $this->stages[0]->delete();
            foreach ($stages as $stageName) {
                $this->stages()->create([
                    'user_id' => $this->user_id,
                    'team_id' => $this->team_id,
                    'name' => $stageName
                ]);
            }
        }
    }

    public function findOrCreateField($fieldData) {
        $fieldToSearch = isset($fieldData['name']) ? 'name' : 'id';
        $valueToSearch = $fieldData['name'] ?? $fieldData['field_id'] ?? "";
        $field = $this->fields()->where(["$fieldToSearch" =>  "$valueToSearch"])->limit(1)->get();
        $field = count($field) ? $field[0] : null;
        if (!$field) {
            $field = $this->fields()->create([
                'user_id' => $this->user_id,
                'team_id' => $this->team_id,
                'name' => $fieldData['name'],
                'title' => empty($fieldData['title']) ? \ucwords(str_replace("_", " ", $fieldData['name'])) : $fieldData['title'],
                'type' => $fieldData['type'] ?? 'text',
                'hide' => $fieldData['hide'] ?? 0,
            ]);
        }
        return $field;
    }

    public function deleteStages() {
        $stages = $this->stages();

        foreach ($stages as $stage) {
            $stage->deleteRelated();
        }
        $stages->delete();
    }
}
