[![](https://poggit.pmmp.io/shield.state/CustomNPC)](https://poggit.pmmp.io/p/CustomNPC)
<a href="https://poggit.pmmp.io/p/CustomNPC"><img src="https://poggit.pmmp.io/shield.state/CustomNPC"></a>
[![](https://poggit.pmmp.io/shield.api/CustomNPC)](https://poggit.pmmp.io/p/CustomNPC)<a href="https://poggit.pmmp.io/p/CustomNPC"><img src="https://poggit.pmmp.io/shield.api/CustomNPC"></a>
<h2><a id="user-content-description" class="anchor" aria-hidden="true" href="#description"><svg class="octicon octicon-link" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5 3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"></path></svg></a id='desc'>[Description]</h2>

  <li>
    Click on above picture to see tutorial video of the plugin.
  </li>
  <br>

  <li>
    These are two examples about how to add commands with above tags into NPC
  </li>
  
For the first (Will show the player's money if EconomyAPI installed on server):
  <code>{rca} {player} mymoney</code>

And for the latter (Send a message with player's name from console):
  <code>say Hello my name is {player}</code></p>
<h2><a id="user-content-usage" class="anchor" aria-hidden="true" href="#usage"><svg class="octicon octicon-link" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5 3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"></path></svg></a>[Usage]</h2>
<ol>
<li>Put phar from Poggit into <code>plugins</code> folder</li>
<li>Edit the Settings.yml file of plugin in <code>plugin_data/CustomNPC</code></li>
  <li>You can change message and more stuff for the plugin in <code>Settings.yml</code></li>
  <li>To spawn NPC use this command</li>
<ul>
  <li>/rca
  <ul>
    <li>Permission: customnpc.rca.permission</li>
    <li>Default: Op</li>
  </ul>
    <li>/npc
  <ul>
    <li>Permission: customnpc.permission</li>
    <li>Default: Op</li>
  </ul>
  <li>
    Note: To enable editor mode you need to use this command: <code>/npc edit</code>
  </li>
</li>
</ul>
</li>
</ol>
<p>Hope you enjoy using this plugin!<p>
<h2>[Libraries]</h2>
<il>
<ul>
<li><a href="https://github.com/jojoe77777/FormAPI/">FormAPI</a></li>
</ul>
</li>
</ol>
