<style type="text/css">
	*{font-family: 'Lato', sans-serif;}
	.error_page h1 {
	    font-size: 150px;
	    line-height: 1;
	    margin: 0;
	}
	.error_page {
	    width: 400px;
	    text-align: center;
	    margin: 50px auto;
	}
	.error_page h4 {
		margin: 0;
		font-size: 23px;
	}
	.error_page a {
		background: #000;
		display: inline-block;
		color: #fff;
		text-decoration: none;
		padding: 15px 20px;
		margin-top: 10px;
	}
</style>
<div id="main">
	<div class="error_page">
    	<h1>404</h1>
    	<h4>Page not Found</h4>
    	<p>We're sorry, the page you requested could not be found. Please go back to the homepage.</p>
    	<a href="{{ url('admin/') }}">Go to Homepage</a>
	</div>
</div>