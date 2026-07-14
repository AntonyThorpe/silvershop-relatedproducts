<% if $RelatedProducts %>
    <div id="RelatedProducts">
    	<h3>Related Products</h3>
    	<ul>
    		<% loop $RelatedProducts %>
    			<% include SilverShop\Includes\ProductGroupItem %>
    		<% end_loop %>
    	</ul>
    </div>
<% end_if %>
