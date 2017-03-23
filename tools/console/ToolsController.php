<?php

namespace hiqdev\php\billing\tools\console;

use Symfony\Component\Yaml\Yaml;
use Yii;

class ToolsController extends \hidev\controllers\CommonController
{

    public function actionRes()
    {
        $src  = Yii::getAlias('@hiqdev/php/billing/res/units.yml');
        $tree = Yaml::parse(file_get_contents($src));
        $this->prepareUnits('', $tree);
        $dump = var_export($this->units, true);
        
        $dst  = Yii::getAlias('@hiqdev/php/billing/res/units.php');
        file_put_contents($dst, "<?php\n\nreturn $dump;\n");
    }

    protected $units = [];

    protected function prepareUnits($parent, $units)
    {
        foreach ($units as $name => $data) {
            if (is_array($data)) {
                $this->units[$name] = [
                    'parent' => $parent,
                    'factor' => 1,
                ];
                $this->prepareUnits($name, $data);
            } else {
                if ($name === $parent) {
                    $this->units[$name]['factor'] = $data;
                } else {
                    $this->units[$name] = [
                        'parent' => $parent,
                        'factor' => $data,
                    ];
                }
            }
        }
    }
}
