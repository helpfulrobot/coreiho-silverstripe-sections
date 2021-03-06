<?php
class LinkSection extends Section
{
    /**
     * Database fields
     * @return array
     */
    private static $db = array(
        'Title' => 'Text',
        'LinkType' => 'Enum("currentchildren,specify,children", "currentchildren")'
    );

    /**
    * Has one relationship
    * @return array
    */
    private static $has_one = array(
        'ParentPage' => 'Page'
    );

    /**
    * Many_many relationship
    * @return array
    */
    private static $many_many = array(
        'LinkList' => 'SectionsLink'
    );

    private static $many_many_extraFields = array(
        'LinkList' => array(
            'Sort' => 'Int'
        )
    );

    /**
     * CMS Fields
     * @return array
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $TeaserConfig = GridFieldConfig_RecordEditor::create();
        if ($this->LinkList()->Count() > 0) {
            $TeaserConfig->addComponent(new GridFieldOrderableRows());
        }

        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextareaField::create(
                    'Title'
                )->setRows(1),
                DropdownField::create(
                    'LinkType',
                    'Type',
                    array(
                        "currentchildren" => "List all sub pages of this page",
                        "children" => "Specify a page and list all its sub pages",
                        "specify" => "Specify each link"
                    )
                ),
                DisplayLogicWrapper::create(
                    TreeDropdownField::create(
                        'ParentPageID',
                        'Select a page',
                        'SiteTree'
                    )
                )->displayIf("LinkType")->isEqualTo("children")->end(),
                DisplayLogicWrapper::create(
                    GridField::create(
                        'LinkList',
                        'Current Link(s)',
                        $this->LinkList(),
                        $TeaserConfig
                    )
                )->displayIf("LinkType")->isEqualTo("specify")->end()
            )
        );
        return $fields;
    }

    public function ListLinks()
    {
        switch ($this->LinkType) {
            case 'specify':
                return $this->LinkList();
                break;
            case 'children':
                $currentPage = Director::get_current_page();
                return $this
                    ->ParentPage()
                    ->Children()
                    ->Exclude(
                        array(
                            "ID" => $currentPage->ID
                        )
                    );
                break;
            case 'currentchildren':
            default:
                $currentPage = Director::get_current_page();
                return $currentPage->Children();
                break;
        }
    }
}
