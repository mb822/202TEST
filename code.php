<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
  //  flash("You don't have permission to access this page");
  //  die(header("Location: login.php"));
//}
?>
<?php
$query = "";
$results = [];
$sort = "";

if(isset($_POST["sort_by"])){
    $sort = $_POST["sort_by"];
}

if (isset($_POST["query"])) {
    $query = $_POST["query"];
}

if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();

    if(has_role("Admin")){
        if($query == "ALL"){$stmt = $db->prepare("SELECT id, name, quantity, price, description, user_id from Products");}
        else{$stmt = $db->prepare("SELECT id, name, quantity, price, description, user_id from Products WHERE name like :q OR category=  :q");}
    }
    else{
//	flash(var_dump($sort));
//	flash(var_dump($sort=="0"));
//
  	if(strcmp($sort, "hl")==0){$stmt = $db->prepare("SELECT id, name, quantity, price, description, user_id from Products WHERE (name like :q OR category = :q) AND visibility = 1 LIMIT 10 ORDER BY price DESC");}
        elseif(strcmp($sort, "lh")==0){$stmt = $db->prepare("SELECT id, name, quantity, price, description, user_id from Products WHERE (name like :q OR category = :q) AND visibility = 1 LIMIT 10 ORDER BY price ASC ");}
        else{$stmt = $db->prepare("SELECT id, name, quantity, price, description, user_id from Products WHERE (name like :q OR category = :q) AND visibility = 1 LIMIT 10");}
    }


    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
	flash("There was a problem fetching the results");
    }
}
?>
<form method="POST">
    <label for= "pleasesignin">Search Inventory.</label>
    <input name="query" placeholder="Search by Name or Category" value="<?php safer_echo($query); ?>"/>

        <select name="sort_by" id="sort_by"  value="<?php safer_echo($sort); ?>" >
            <option value="none">Sort by: None</option>
            <option value="hl" >Price: High to Low</option>
            <option value="lh" >Price: Low to High</option>


    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>Name:</div>
                        <div><?php safer_echo($r["name"]); ?></div>
                    </div>
                    <div>
                        <div>Price:</div>
                        <div><?php safer_echo("$".$r["price"]); ?></div>
                    </div>
                    <div>
                        <div>Description:</div>
                        <div><?php safer_echo($r["description"]); ?></div>
                    </div>
                    <div>

                        <?php if (has_role("Admin")): ?>
                                <a type="button" href="test_edit_product.php?id=<?php safer_echo($r['id']); ?>">Edit</a>|
                        <?php endif; ?>

                        <a type="button" href="test_view_products.php?id=<?php safer_echo($r['id']); ?>">View</a>|
                        <a type="button" href="add_to_cart.php?id=<?php safer_echo($r['id']); ?>">Add to bag</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>

