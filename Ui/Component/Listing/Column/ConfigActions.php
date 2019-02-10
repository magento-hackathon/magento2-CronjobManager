<?php
namespace EthanYehuda\CronjobManager\Ui\Component\Listing\Column;

use Magento\Framework\Exception\NotFoundException;
use Magento\Ui\Component\Listing\Columns\Column;

class ConfigActions extends Column
{
    const JOB_CODE = 'job_code';
    
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                if(!isset($item[self::JOB_CODE]))
                {
                    throw new NotFoundException(__(
                        'Missing Job Code: %1.', 
                        $item[self::JOB_CODE]
                        )
                    );
                }
                $item[$name] = [
                    'edit' => [
                        "href" => $this->getContext()->getUrl(
                            "cronjobmanager/config/edit", [
                                'job_code' => $item[self::JOB_CODE]
                            ]),
                        "label"=>__("Edit")
                    ],
                    'enable' => [
                        "href" => $this->getContext()->getUrl(
                            "cronjobmanager/config/enable", [
                                'job_code' => $item[self::JOB_CODE]
                            ]),
                        "label"=>__("Enable")
                    ],
                    'disable' => [
                        "href" => $this->getContext()->getUrl(
                            "cronjobmanager/config/disable", [
                                'job_code' => $item[self::JOB_CODE]
                            ]),
                        "label"=>__("Disable")
                    ],
                ];
            }
        }
        return $dataSource;
    }  
}
