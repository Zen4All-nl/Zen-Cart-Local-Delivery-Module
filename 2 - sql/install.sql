--
-- Table structure for table `default_delivery_time`
--

CREATE TABLE `default_delivery_time` (
  `default_id` int(11) NOT NULL,
  `day_id` int(11) NOT NULL DEFAULT '0',
  `slot_id` int(11) NOT NULL DEFAULT '0',
  `cost` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `max_limit` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders_delivery_time`
--

CREATE TABLE `orders_delivery_time` (
  `orders_id` int(11) NOT NULL,
  `delivery_time_slot_id` int(11) DEFAULT '0',
  `delivery_date` date NOT NULL DEFAULT '0001-01-01',
  `delivery_cost` decimal(15,4) NOT NULL DEFAULT '0.0000'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `special_delivery_time`
--

CREATE TABLE `special_delivery_time` (
  `id` int(10) NOT NULL,
  `special_delivery_date` date NOT NULL DEFAULT '0000-00-00',
  `slot_id` int(11) NOT NULL DEFAULT '0',
  `special_cost` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `special_max_limit` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `default_delivery_time`
--
ALTER TABLE `default_delivery_time`
  ADD PRIMARY KEY (`default_id`);

--
-- Indexes for table `orders_delivery_time`
--
ALTER TABLE `orders_delivery_time`
  ADD PRIMARY KEY (`orders_id`),
  ADD KEY `idx_status_orders_cust_zen` (`orders_id`),
  ADD KEY `idx_cust_id_orders_id_zen` (`orders_id`);

--
-- Indexes for table `special_delivery_time`
--
ALTER TABLE `special_delivery_time`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `default_delivery_time`
--
ALTER TABLE `default_delivery_time`
  MODIFY `default_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `special_delivery_time`
--
ALTER TABLE `special_delivery_time`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;


INSERT INTO `admin_pages` (`page_key`, `language_key`, `main_page`, `page_params`, `menu_key`, `display_on_menu`, `sort_order`) VALUES
('defaultDeliveryTime', 'BOX_MODULES_DEFAULT_DELIVERY_TIME', 'FILENAME_DEFAULT_DELIVERY_TIME', '', 'modules', 'Y', 4),
('specialDeliveryTime', 'BOX_MODULES_SPECIAL_DELIVERY_TIME', 'FILENAME_SPECIAL_DELIVERY_TIME', '', 'modules', 'Y', 5); 