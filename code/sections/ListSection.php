<?php
class ListSection extends Section
{
    /**
     * Has_many relationship
     * @return array
     */
    private static $many_many = array(
        "Items" => "SectionsListItem"
    );

    private static $many_many_extraFields = array(
        "Items" => array(
            "Sort" => "Int"
        )
    );

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $listGridConfig = GridFieldConfig_RelationEditor::create();
        if ($this->Items()->Count() > 0) {
            $listGridConfig->addComponent(new GridFieldOrderableRows());
        }

        $fields->addFieldToTab(
            'Root.Main',
            GridField::create(
                'Items',
                'List',
                $this->Items(),
                $listGridConfig
            )
        );
        return $fields;
    }
}
