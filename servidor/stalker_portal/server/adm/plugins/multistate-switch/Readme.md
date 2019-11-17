# SwitchStateButton
A jquery plugin for button with multiple states.

Demo: http://arth-ur.github.io/SwitchStateButton/

Download: https://github.com/arth-ur/SwitchStateButton/archive/gh-pages.zip

##Installation
Include tsb.css in your file :
```
<link rel="stylesheet" href="css/tsb.css"/>
```
and tsb.js :
```
<script type ="text/javascript" src="js/tsb.js"></script>
```

##Usage
First, create a div:
```
<div id="button" />
```
Then call the plugin with `$('...').threestatebutton(args)`:
```
$("#button").threestatebutton({
  statecount: 3,
  state: 1,
  stateChanged: function(state, source){
    console.log(state + " from " + source);
  }
});
```
##Parameters
The plugin arguments are:

 Parameter | Description 
 --------- | ----------- 
statecount|number of states
state|initial state, from 0 included to `statecount` excluded
stateChanged|a callback function called with arguments `state` the new state and `source`, the source button
