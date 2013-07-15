USE [ASD]
GO

/****** Object:  Table [dbo].[delivery_table]    Script Date: 06/07/2013 19:56:08 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[delivery_table](
	[transaction_id] [varchar](255) NOT NULL,
	[account_name] [varchar](255) NOT NULL,
	[char_name] [varchar](255) NOT NULL,
	[item_ids] [varchar](225) NOT NULL,
	[delivery_time] [varchar](255) NOT NULL,
	[credits_used] [bigint] NOT NULL,
	[coupon_code] [varchar](255) NOT NULL,
	[ip_address] [varchar](255) NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

