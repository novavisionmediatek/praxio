<?php
/** Initial site content, taken from the original static pages. Loaded by setup.php. */
$U = fn(string $id, int $w = 700) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w={$w}&q=75";

return [
    'settings' => [
        'contact_email'      => 'mohit.gandhi@praxia.co.in',
        'contact_phone'      => '+91 86306 15592',
        'company_legal_name' => 'Novus Procura India Private Limited',
        'notification_email' => 'mohit.gandhi@praxia.co.in',
        'team_size'          => '17',
    ],

    'partners' => ['IDFC FIRST Bank', 'Tata Capital', 'Aditya Birla Capital', 'Cholamandalam', 'SBM Bank', 'Federal Bank', 'South Indian Bank', 'InCred', 'HSBC', 'Barclays', 'Citi', 'HDFC Bank', 'Axis Bank'],

    // placement, title, description, image
    'asset_categories' => [
        ['home', 'Industrial machinery', 'Manufacturing lines and plant equipment for electronics, auto, chemicals and metals.', $U('1504917595217-d4dc5ebe6122')],
        ['home', 'Vehicles & fleet', 'Commercial vehicles and logistics assets, procured and delivered across locations.', $U('1565793298595-6a879b1d9492')],
        ['home', 'Renewable energy', "Solar, wind and storage assets for India's fastest growing infrastructure segment.", $U('1532601224476-15c79f2f7a51')],
        ['home', 'Equipment & tech', 'Precision tooling, electronics and IT infrastructure with custom rental structures.', $U('1581092160562-40aa08e78837')],
        ['product', 'Manufacturing', 'Electronics, automotive, chemicals, pharma and metals.', $U('1504917595217-d4dc5ebe6122', 600)],
        ['product', 'Fleet & logistics', 'Vehicles and logistics equipment.', $U('1565793298595-6a879b1d9492', 600)],
        ['product', 'Construction & mining', 'Heavy equipment across sites.', $U('1578575437130-527eed3abbec', 600)],
        ['product', 'Renewables', 'Solar, wind and storage assets.', $U('1532601224476-15c79f2f7a51', 600)],
    ],

    // label, value, prefix, suffix
    'stats' => [
        ['Assets under management', 226, '₹', ' Cr'],
        ['Corporate customers', 15, '', ''],
        ['Years of combined team experience', 45, '', '+'],
        ['NPA track record', 0, '', ''],
    ],

    // image, alt, tag, title, metrics, challenge, solution, outcome, home_order, home_tag, home_title, home_kpis
    'case_studies' => [
        [$U('1504917595217-d4dc5ebe6122', 1000), 'Automotive manufacturing', 'Automotive · Credit rating AA+',
         'Streamlining asset procurement for an automotive OEM leader',
         "25|plants onboarded\n₹150 Cr|credit line\n₹35 Cr|transactions",
         'Centralised procurement limited unit-level visibility on costs and profitability.',
         'Decentralised leasing with PO creation management, unit-level budgets and payment responsibility.',
         'Improved agility and financial transparency, with reduced procurement man-hours.',
         1, 'Automotive OEM', 'Decentralised procurement across 25 plants', "₹150 Cr|credit line\n25|plants"],
        [$U('1560179707-f14e90ef3623', 1000), 'Corporate towers', 'Real estate · Credit rating A+',
         'Enabling asset-light transformation for a real estate leader',
         "₹15 Cr|transactions\n₹35 Cr|credit line",
         'Asset-heavy model, desire for low debt and a need for hassle-free service.',
         'Operating lease for machinery and equipment across multiple work sites, with debt-free acquisition.',
         'Successful shift to an asset-light model with low debt maintained.',
         3, 'Real estate', 'Asset-light transformation without adding debt', "₹15 Cr|transactions\nA+|credit rating"],
        [$U('1578575437130-527eed3abbec', 1000), 'Port cranes and containers', 'Construction & mining · Credit rating A+',
         'Empowering growth for a construction and mining equipment distributor',
         "₹130 Cr+|transactions\n3|lenders onboarded",
         'Earlier leasing focused solely on machines, and traditional lenders capped leverage.',
         'A comprehensive leasing solution with three lenders onboarded.',
         'Increased credit line and a streamlined leasing process that supports scale.',
         2, 'Construction & mining', 'Three lenders onboarded for an equipment leader', "₹130 Cr+|transactions\n3|lenders"],
        [$U('1532601224476-15c79f2f7a51', 1000), 'Green fields', 'Dairy · 16 lakh litres/day',
         'Enhancing financial flexibility for a leading dairy company',
         "7%|better than competitor quotes\n5,000|villages in network",
         'Low sector margins, limited financier exposure and a need to protect debt-to-equity.',
         'Operating lease with Ind AS 116 non-applicability, exposure to top NBFCs and a custom repayment structure.',
         'Preserved debt-to-equity ratio, more financier options and better cash flow management.',
         null, null, null, null],
    ],

    // tier, initials, name, role, bio
    'team_members' => [
        ['founder', 'MG', 'Mohit Gandhi', 'Co-founder & CEO', "Ex-Head, Retail & Strategic Partnerships, OfBusiness\nEx-Senior Consultant, PwC Strategy&\nEx-Maruti Suzuki\nMBA FMS Delhi · B.Tech VIT Vellore"],
        ['founder', 'SK', 'Saurabh Kanojia', 'Co-founder & COO', "Ex-Cluster Lead, Urban Company\nEx-Lead, Supply Chain, Amazon Business\nEx-Wipro\nMBA IIM Ahmedabad · B.Tech NIT Trichy"],
        ['founder', 'AG', 'Adarsh Gautam', 'Co-founder & CBO', "Ex-EIR, Urban Company\nEx-DGM, Bira91\nEx-Unilever, Maruti Suzuki\nMBA IIM Ahmedabad · B.Tech NIT Jamshedpur"],
        ['founder', 'KV', 'Kunal Varma', 'Co-founder & CFO', "Ex-Director, Strategic Business, Cashflo\nEx-Lead, Group Strategy, DBS Bank\nEx-VP, Trade Sales, HSBC\nMBA IIM Lucknow"],
        ['leader', 'DA', 'Dilip Akalankam', 'Strategy · Finance · BD', "Ex-VP, Treasury & Trade Solutions, Citi\nEx-AVP, Deutsche Bank\nMBA IIM Kozhikode · B.Tech IIT Madras"],
        ['leader', 'VD', 'Viren Dargar', 'Growth · Channel Partnerships', "Ex-Avendus, BCG, Grip Invest\nMBA IIM Lucknow · Chartered Accountant"],
        ['leader', 'PK', 'Papiha Kalra', 'Finance & Legal Operations', "Ex-Josh Talks, Third Wave Power\nFP&A, Master's Union · ICAI Accounting Technician"],
        ['leader', 'AS', 'Anukriti Sharma', 'Finance Partnerships · Credit', "Ex-Branch Credit Head, Union Bank of India\nEx-Compliance Manager, Union Bank\nIndian Institute of Banking & Finance"],
    ],

    'investors' => [
        ['Whiteboard Capital', '₹500 Cr fund · Investors in Cred, Progcap, Dezerv'],
        ['Alteria Capital', "India's largest venture debt fund · ₹6,000 Cr+ deployed"],
        ['OG Capital', '₹300 Cr fund · Exits exceeding ₹1,500 Cr'],
    ],

    // quote, author, designation, initials
    'testimonials' => [
        ['No impact on debt leverage and the customization of lease rentals meant that working with Praxia is a no brainer.', 'Parimal Shah', 'Founder, Cherise India', 'PS'],
        ['Praxia understood our business and budget requirements and converted them into cost effective leasing terms, handling end-to-end procurement and multi-location delivery.', 'Vikash Dugar', 'CFO, Ashiana Housing', 'VD'],
        ['Their vast network of financial institutions, coupled with streamlined processes, has significantly enhanced our financial capabilities.', 'Deepak', 'CFO, CRPL (Reliance Brands subsidiary)', 'D'],
        ['Traditional financing often requires collateral, restrictive covenants and lengthy processes. Leasing removed that friction.', 'Amit Kumar', 'GM, Sandhar Tech', 'AK'],
    ],

    // category, question, answer (HTML allowed), home_answer (short version for the home page, or null; null = not on home)
    'faqs' => [
        ['lease', 'What is an operating lease?', 'Praxia procures the asset you need and leases it to you for an agreed tenure. You use the asset and pay rentals without the burden of ownership.', 'Praxia procures the asset and leases it to you for an agreed tenure. You use it and pay rentals without the burden of ownership.'],
        ['lease', 'Which assets can be leased?', 'Moveable CapEx assets such as machinery, equipment and vehicles. We focus on electronics, automotive, chemicals, pharma, metals and renewables, and have also funded construction equipment and real-estate site machinery.', null],
        ['fin', 'Is a down payment required?', 'No. Lease structures offer 100% funding, so there is no down payment or margin money.', 'No. The structure offers 100% funding with no down payment.'],
        ['fin', 'Does leasing affect my debt-to-equity ratio?', "Operating leases are off-balance sheet financing, so they don't add to your debt leverage or covenants. A dairy customer preserved its debt-to-equity ratio because Ind AS 116 did not apply to its structure. Applicability depends on your accounting position, so please confirm with your auditors.", "Operating leases are off-balance sheet, so they don't add to your debt or covenants. Please confirm accounting treatment with your auditors."],
        ['fin', 'What are the tax benefits?', 'Operating lease rentals are treated as an operating expense and are tax-deductible, lowering the true post-tax cost compared with a loan.', null],
        ['fin', 'Are there hidden fees?', 'No. We quote one transparent price with zero hidden fees.', null],
        ['process', 'How long does approval take?', "Typically weeks, not months, depending on documentation and the financier's credit process.", 'In weeks, not months, with one transparent price and no hidden fees.'],
        ['process', 'Who handles procurement and delivery?', "Praxia manages procurement end to end, including multi-location delivery, so your operations aren't disrupted.", null],
        ['process', 'Can rentals match my cash cycle?', 'Yes. Rentals can be monthly or quarterly and shaped to your business cycle.', null],
        ['lender', 'Which banks and NBFCs do you work with?', 'We have executed deals with IDFC FIRST Bank, Tata Capital, Aditya Birla Capital, Cholamandalam, SBM Bank, Federal Bank, South Indian Bank and InCred, with further partnerships in progress.', null],
        ['lender', 'How can a financial institution partner with Praxia?', 'Praxia originates, structures and converts the customer, giving you asset-backed mid-market exposure with faster recovery on default. <a href="contact.php" class="link">Contact us</a> to get started.', null],
    ],

    // location, label, url
    'menu_items' => [
        ['header', 'Home', 'index.php'], ['header', 'Product', 'product.php'], ['header', 'About Us', 'about.php'],
        ['header', 'Case Studies', 'case-studies.php'], ['header', 'FAQ', 'faq.php'], ['header', 'Careers', 'careers.php'],
        ['footer_company', 'About Us', 'about.php'], ['footer_company', 'Our Team', 'about.php#team'], ['footer_company', 'Careers', 'careers.php'], ['footer_company', 'Contact', 'contact.php'],
        ['footer_explore', 'Operating Lease', 'product.php'], ['footer_explore', 'Case Studies', 'case-studies.php'], ['footer_explore', 'FAQ', 'faq.php'],
    ],

    // placement, icon, title, description
    'features' => [
        ['home', 'card', 'Off-balance sheet', 'Unlock growth without impacting your debt covenants.'],
        ['home', 'wave', 'Flexible cash flows', 'Structure payments to match your business cycle.'],
        ['home', 'coin', 'Capital-light', 'Acquire critical assets with no upfront investment.'],
        ['home', 'doc', 'Tax efficient', 'Benefit from fully deductible operating expenses.'],
        ['home', 'bolt', 'Transparent & fast', 'One price, zero hidden fees, executed in weeks, not months.'],
        ['home', 'target', 'End-to-end delivery', "We handle procurement and multi-location delivery so operations don't stop."],
        ['careers', 'bars', 'High-growth', 'Rapid AUM expansion with a target of ₹2,500 Cr by March 2028.'],
        ['careers', 'team', 'Learn from the best', 'Work directly with founders from PwC, HSBC, DBS, Amazon and Urban Company.'],
        ['careers', 'shield', 'Ownership', 'A lean team where every person owns real outcomes.'],
        ['diligence', 'chart', 'Financial', 'Balance sheets are reviewed for anomalies and long-term leasing support before we evaluate the business.'],
        ['diligence', 'team', 'Business model', "Our founders' diverse backgrounds assess the long-term sustainability of the customer's model."],
        ['diligence', 'shield', 'Asset', "We assess value through the lease and in the secondary market so the financier's outstanding stays below asset value."],
    ],

    // placement, title, description
    'steps' => [
        ['home', 'Share your need', 'Tell us the asset, vendor and budget.'],
        ['home', 'We structure', 'Praxia designs the lease and matches the right financier.'],
        ['home', 'Quick approval', 'Financial, business-model and asset checks.'],
        ['home', 'Assets delivered', 'We procure and deliver, then monitor through the lease.'],
        ['product', 'Requirement', 'Asset, vendor, tenure and budget.'],
        ['product', 'Structuring', 'Custom rentals and lender matching.'],
        ['product', 'Approval', 'Financial, business and asset checks.'],
        ['product', 'Delivery', 'Procurement, delivery and monitoring.'],
    ],

    // aspect, loan, lease
    'compare_rows' => [
        ['Upfront cost', 'Down payment / margin money', 'No down payment'],
        ['Balance sheet', 'Adds to debt', 'Off-balance sheet'],
        ['Repayment', 'Rigid EMIs', 'Customised monthly / quarterly'],
        ['Tax', 'Interest + depreciation', 'Rentals deductible'],
        ['Covenants', 'Restrictive', 'No leverage impact'],
        ['Timeline', 'Lengthy processes', 'Weeks, not months'],
    ],

    // title, department, type, description
    'job_openings' => [
        ['Business Development Manager', 'Sales', 'Full-time', 'Sample role: replace with a real opening from the admin panel.'],
        ['Credit Analyst', 'Credit & Risk', 'Full-time', 'Sample role: replace with a real opening from the admin panel.'],
        ['Operations Associate', 'Operations', 'Full-time', 'Sample role: replace with a real opening from the admin panel.'],
    ],
];
