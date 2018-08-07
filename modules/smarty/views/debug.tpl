        <div id="debugSmarty">
            <h2>
            	Smarty debug Console  -  Total Time
            	<!--{$execution_time|string_format:"%.5f"}-->
            </h2>
            
            <h3>included templates &amp; config files (load time in seconds)</h3>
            
            <div>
              <!--{foreach $template_data as $template}-->
              	<span class="txtBrun">
                	<!--{$template.name}-->
              	</span> 
              	<span class="exectime"> (compile
                    <!--{$template['compile_time']|string_format:"%.5f"}-->
                    ) (render
                    <!--{$template['render_time']|string_format:"%.5f"}-->
                    ) (cache
                    <!--{$template['cache_time']|string_format:"%.5f"}-->
              		) 
                </span> 
                <br />
              <!--{/foreach}-->
            </div>
            
            <!--{if count($assigned_vars) != 0}-->
                <h3>assigned template variables</h3>
                
                <table id="table_assigned_vars">
                    <tbody>
                        <!--{foreach $assigned_vars as $vars}-->
                            <tr class="<!--{if $vars@iteration % 2 eq 0}-->odd<!--{else}-->even<!--{/if}-->">
                              <th>$
                                <!--{$vars@key|escape:'html'}--></th>
                              <td><!--{$vars|debug_print_var|replace:'<br>':'<br />'}--></td>
                            </tr>
                        <!--{/foreach}-->
                      </tbody>
                </table>
            <!--{/if}-->
            
            <!--{if count($config_vars) != 0}-->
                <h3>assigned config file variables (outer template scope)</h3>
                
                <table id="table_config_vars">
                    <tbody>    
                        <!--{foreach $config_vars as $vars}-->
                            <tr class="<!--{if $vars@iteration % 2 eq 0}-->odd<!--{else}-->even<!--{/if}-->">
                              <th><!--{$vars@key|escape:'html'}--></th>
                              <td><!--{$vars|debug_print_var|replace:'<br>':'<br />'}--></td>
                            </tr>
                        <!--{/foreach}-->
                    </tbody>
                </table>
            <!--{/if}-->
        </div>
	</body>
</html>