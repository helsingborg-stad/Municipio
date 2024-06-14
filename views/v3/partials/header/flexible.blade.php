@extends('templates.header', ['classList' => ['c-header']])
    <style>
  .grid-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.item1 {
    grid-row: span 2;
    display: flex;
    justify-content: center;
    align-items: center;
}

.item2::after,
.item3::after {
    content: "";
    position: absolute;
    width: 300vw;
    left: 0;
    right: 0;
    transform: translateX(-50%);
    height: 100%;
    z-index: -1;
}

.item2::after {
    background-color: white;
}
.item3::after {
    background-color: pink;
}

.item2, .item3 {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}
    </style>
  <div class="o-container grid-container">
        <div class="item1">Item 1</div>
        <div class="item2">Item 2</div>
        <div class="item3">Item 3</div>
    </div>
