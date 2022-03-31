<?php
global $wpdb;
global $table_prefix;
$table_name=$table_prefix."payouts";

$total_sql="select count(*) as total from $table_name";
$total_transactions=$wpdb->get_results($total_sql);

$total_rows=empty($total_transactions)?0:$total_transactions[0]->total;


if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
$no_of_records_per_page = 10;
$offset = ($pageno-1) * $no_of_records_per_page; 
$total_pages = ceil($total_rows / $no_of_records_per_page);

$sql="select *from $table_name LIMIT $offset, $no_of_records_per_page";
$transactions=$wpdb->get_results($sql);

?>
<div class="wrap">
	<h1>Awepay Payout Plugin</h1>
	

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1">Transactions</a></li>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">

			<table class="transactions table-striped table">
                <tr>
                    <th>TID</th>
                    <th>TXID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Bank code</th>
                    <th>Account name</th>
                    <th>Account number</th>
                    <th>Status</th>
                </tr>
                <?php
                   foreach($transactions as $tran){
                    echo "<tr> <td>".strtoupper($tran->tid)."</td>
                    <td><a target='_blank' href='".TRANSACTION_URL."$tran->txid'>$tran->txid</a></td>
                    <td>$tran->firstname</td>
                    <td>$tran->lastname</td>
                    <td>$tran->email</td>
                    <td>$tran->amount</td>
                    <td>$tran->currency</td>
                    <td>$tran->bank_code</td>
                    <td>$tran->account_name</td>
                    <td>$tran->account_number</td>
                    <td>$tran->status</td>
                    </tr>
                    ";
                   } 

                ?>
                <tr>
                    <td colspan="12">
                        <?php if($total_rows>0){ ?>
                        <ul class="pagination">
                        <li><a href="?page=awepay_payout_transactions&pageno=1">First</a></li>
                        <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                        <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?page=awepay_payout_transactions&pageno=".($pageno - 1); } ?>">Prev</a>
                        </li>
                        <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?page=awepay_payout_transactions&pageno=".($pageno + 1); } ?>">Next</a>
                        </li>
                        <li><a href="?page=awepay_payout_transactions&pageno=<?php echo $total_pages; ?>">Last</a></li>
                        </ul>
                        <?php } ?>
                    </td>
                </tr>
            </table>
			
		</div>

		
	</div>
</div>