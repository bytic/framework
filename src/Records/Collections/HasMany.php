<?php

class Nip_RecordCollection_HasMany extends Nip_RecordCollection_Associated
{

    public function save()
    {
        if (count($this)) {
            $pk = $this->getManager()->getPrimaryKey();
            $fk = $this->getParam("fk");

            foreach ($this as $item) {
                $item->$fk = $this->getItem()->$pk;
            }
            parent::save();
        }
    }

    public function remove($record)
    {
        parent::remove($record);
        $record->delete();
        return $this;
    }

}