
<% if Items.MoreThanOnePage %>
<p class="Pagination">
	<% if Items.NotFirstPage %>
		<a class="NotExternal Prev" href="$Items.PrevLink">
			<span class="Inner"><% _t('PaginationFor.ss.PREV', '&laquo; Previous') %></span>
		</a>
	<% else %>
		<span class="Prev"><span class="Inner"></span></span>
	<% end_if %>
	<span class="List">
		<span class="Inner">
		<% control Items.Pages %>
			<a class="NotExternal Page <% if CurrentBool %>current<% else %>link<% end_if %>" href="$Link">$PageNum</a>
		<% end_control %>
		</span>
	</span>
	<% if Items.NotLastPage %>
		<a class="NotExternal Next" href="$Items.NextLink">
			<span class="Inner"><% _t('PaginationFor.ss.NEXT', 'Next  &raquo;') %></span>
		</a>
	<% else %>
		<span class="Next"><span class="Inner"></span></span>
	<% end_if %>
	
	<span class="Clear"></span>
</p>
<% end_if %>

