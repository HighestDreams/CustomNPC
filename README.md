[![](https://poggit.pmmp.io/shield.state/CustomNPC)](https://poggit.pmmp.io/p/CustomNPC)
<a href="https://poggit.pmmp.io/p/CustomNPC"><img src="https://poggit.pmmp.io/shield.state/CustomNPC"></a>
[![](https://poggit.pmmp.io/shield.api/CustomNPC)](https://poggit.pmmp.io/p/CustomNPC)<a href="https://poggit.pmmp.io/p/CustomNPC"><img src="https://poggit.pmmp.io/shield.api/CustomNPC"></a>
<h2><a id="user-content-description" class="anchor" aria-hidden="true" href="#description"><svg class="octicon octicon-link" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5 3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"></path></svg></a>[Description]</h2>
<img src="https://github.com/HighestDreams/CustomNPC/blob/main/Menu.png">
<p>This is an NPC plugin with super easy customization for pocket-mine.</p>
<p>You need to enable the editor mode to customize your NPC! Using the / npc edit command, you can activate your editor mode and customize your NPC easily!</p>
<p>You can change the size - name using the settings manager button! You can also use the skin change option to change the skin of the NPC to the skin of any of the online players on the server without the need to use commands!</p>
<p>Another important feature of this plugin: You can create customizations for each NPC! This means that you can activate Cool Down or  rotation for each NPC individually! (Via settings manager)</p>
<p>With the command manager system (UI) you can manage the commands of each NPC faster and easier! (Commands manager button)</p>
<p>Important Note: You must use the {rca} tag to execute the command from a player! You can also use the {player} tag to replace the player name!

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
