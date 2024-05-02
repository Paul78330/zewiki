<?php

class Folder {
# -------------------- properties --------------------

    private int $id;
    private int $left_edge;
    private int $right_edge;
    private string $name;
    private int $owner; // foreign key user_id 2->public 3->shares 
    private int $style; // foreing key style_id
    private string $status; // open or closed

# -------------------- Constructor --------------------

    public function __construct($name,$left_edge,$right_edge)
    {
        $this->setName($name);
        $this->setLeftEdge($left_edge);
        $this->setRightEdge($right_edge);
    }

# -------------------- Getters --------------------

    public function getId(): int{return $this->id;}
    public function getLeftEdge(): int {return $this->left_edge;}
    public function getRightEdge(): int {return $this->right_edge;}
    public function getName(): string {return $this->name;}
    public function getType(): string {return 'folder';}
    public function getOwner(): int {return $this->owner;}
    public function getStyle():int {return $this->style;}
    public function getStatus(): string {return $this->status;}

# -------------------- Setters --------------------

    public function setId(int $id): void {$this->id = $id;}
    public function setLeftEdge(int $left_edge): void {$this->left_edge = $left_edge;}
    public function setRightEdge(int $right_edge): void {$this->right_edge = $right_edge;}
    public function setName(string $name): void {$this->name = $name;}
    public function setOwner(int $owner):void {$this->owner = $owner;}
    public function setStyle(int $style):void{$this->style = $style;}
    public function setStatus(string $status): void {$this->status = $status;}

}